<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use MoorlFoundation\Core\Service\LocationService;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MerchantIndexer extends EntityIndexer
{
    private IteratorFactory $iteratorFactory;
    private Connection $connection;
    private EntityRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    private LocationService $locationService;

    public function __construct(
        Connection $connection,
        IteratorFactory $iteratorFactory,
        EntityRepositoryInterface $repository,
        EventDispatcherInterface $eventDispatcher,
        LocationService $locationService
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->repository = $repository;
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->locationService = $locationService;
    }

    public function getName(): string
    {
        return 'moorl_merchant.indexer';
    }

    /**
     * @param array|null $offset
     *
     * @deprecated tag:v6.5.0 The parameter $offset will be native typed
     */
    public function iterate(/*?array */$offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset, 20);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new MerchantIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $ids = $event->getPrimaryKeys(MerchantDefinition::ENTITY_NAME);

        if (empty($ids)) {
            return null;
        }

        return new MerchantIndexingMessage(array_values($ids), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();

        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return;
        }

        $sql = 'SELECT 
LOWER(HEX(moorl_merchant.id)) AS id,
LOWER(HEX(moorl_merchant.country_id)) AS countryId,
moorl_merchant.auto_location AS autoLocation,
moorl_merchant.street AS street,
moorl_merchant.street_number AS streetNumber,
moorl_merchant.zipcode AS zipcode,
moorl_merchant.city AS city,
moorl_merchant.country_code AS iso,
moorl_merchant.location_lat AS lat,
moorl_merchant.location_lon AS lon
FROM moorl_merchant
WHERE moorl_merchant.id IN (:ids);';

        $data = $this->connection->fetchAll(
            $sql,
            ['ids' => Uuid::fromHexToBytesList($ids)],
            ['ids' => Connection::PARAM_STR_ARRAY]
        );

        foreach ($data as $item) {
            if (!$item['countryId'] && $item['iso']) {
                try {
                    $country = $this->locationService->getCountryByIso($item['iso']);
                    if ($country) {
                        $sql = 'UPDATE moorl_merchant SET country_id = :country_id WHERE id = :id;';
                        $this->connection->executeUpdate(
                            $sql,
                            [
                                'id' => Uuid::fromHexToBytes($item['id']),
                                'country_id' => Uuid::fromHexToBytes($country->getId())
                            ]
                        );
                    }
                } catch (\Exception $exception) {}
            }

            if ($item['autoLocation'] === "1") {
                if (!empty($item['lat']) && empty($item['lon'])) {
                    continue;
                }

                $geoPoint = $this->locationService->getLocationByAddress($item);

                if (!$geoPoint) {
                    continue;
                }

                $sql = 'UPDATE moorl_merchant SET location_lat = :lat, location_lon = :lon WHERE id = :id;';
                $this->connection->executeUpdate(
                    $sql,
                    [
                        'id' => Uuid::fromHexToBytes($item['id']),
                        'lat' => $geoPoint->getLatitude(),
                        'lon' => $geoPoint->getLongitude()
                    ]
                );
            }
        }

        $context = Context::createDefaultContext();

        $this->eventDispatcher->dispatch(new MerchantIndexerEvent($ids, $context));
    }
}
