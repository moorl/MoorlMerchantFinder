<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Subscriber;

use Composer\IO\NullIO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Shopware\Core\Content\Cms\CmsPageEvents;
use Moorl\MerchantFinder\Core\GeneralStruct;

class StorefrontSubscriber implements EventSubscriberInterface
{
    private $systemConfigService;
    private $mediaRepository;
    private $connection;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $mediaRepository,
        Connection $connection
    )
    {
        $this->systemConfigService = $systemConfigService;
        $this->mediaRepository = $mediaRepository;
        $this->connection = $connection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageEvents::SLOT_LOADED_EVENT => 'onEntityLoadedEvent'
        ];
    }

    public function onEntityLoadedEvent(EntityLoadedEvent $event): void
    {
        foreach ($event->getEntities() as $entity) {
            if ($entity->getType() == 'moorl-merchant-finder') {
                $languageId = $event->getContext()->getLanguageId();
                $config = $entity->getConfig();

                $countries = null;
                $categories = null;
                $tags = null;
                $productManufacturers = null;

                $countryCode = $this->systemConfigService->get('MoorlMerchantFinder.config.countryCode');

                if ($countryCode) {
                    $sql = <<<SQL
SELECT
    `country_translation`.`name` AS `label`,
    `country`.`iso` AS `value`,
    COUNT(*) AS `count`
FROM `moorl_merchant`
RIGHT JOIN `country` ON `moorl_merchant`.`country_code` = `country`.`iso`
RIGHT JOIN `country_translation` ON `country`.`id` = `country_translation`.`country_id`
WHERE `moorl_merchant`.`active` IS TRUE AND LOWER(HEX(`country_translation`.`language_id`)) = :languageId
GROUP BY `country_translation`.`country_id`
ORDER BY `country_translation`.`name` ASC;
SQL;

                    $countries = $this->connection->executeQuery($sql, ['languageId' => $languageId])->fetchAll(FetchMode::ASSOCIATIVE);
                }

                $sql = <<<SQL
SELECT
    `tag`.`name` AS `label`,
    LOWER(HEX(`tag`.`id`)) AS `value`,
    COUNT(*) AS `count`
FROM `moorl_merchant`
RIGHT JOIN `moorl_merchant_tag` ON `moorl_merchant`.`id` = `moorl_merchant_tag`.`moorl_merchant_id`
RIGHT JOIN `tag` ON `moorl_merchant_tag`.`tag_id` = `tag`.`id`
WHERE `moorl_merchant`.`active` IS TRUE
GROUP BY `tag`.`id`
ORDER BY `tag`.`name` ASC;
SQL;

                $tags = $this->connection->executeQuery($sql)->fetchAll(FetchMode::ASSOCIATIVE);

                $sql = <<<SQL
SELECT
    `product_manufacturer_translation`.`name` AS `label`,
    LOWER(HEX(`moorl_merchant_product_manufacturer`.`product_manufacturer_id`)) AS `value`,
    COUNT(*) AS `count`
FROM `moorl_merchant`
RIGHT JOIN `moorl_merchant_product_manufacturer` ON `moorl_merchant`.`id` = `moorl_merchant_product_manufacturer`.`moorl_merchant_id`
RIGHT JOIN `product_manufacturer_translation` ON `moorl_merchant_product_manufacturer`.`product_manufacturer_id` = `product_manufacturer_translation`.`product_manufacturer_id`
WHERE `moorl_merchant`.`active` IS TRUE AND LOWER(HEX(`product_manufacturer_translation`.`language_id`)) = :languageId
GROUP BY `product_manufacturer_translation`.`product_manufacturer_id`
ORDER BY `product_manufacturer_translation`.`name` ASC;
SQL;

                $productManufacturers = $this->connection->executeQuery($sql, ['languageId' => $languageId])->fetchAll(FetchMode::ASSOCIATIVE);

                $sql = <<<SQL
SELECT
    `category_translation`.`name` AS `label`,
    LOWER(HEX(`moorl_merchant_category`.`category_id`)) AS `value`,
    COUNT(*) AS `count`
FROM `moorl_merchant`
RIGHT JOIN `moorl_merchant_category` ON `moorl_merchant`.`id` = `moorl_merchant_category`.`moorl_merchant_id`
RIGHT JOIN `category_translation` ON `moorl_merchant_category`.`category_id` = `category_translation`.`category_id`
WHERE `moorl_merchant`.`active` IS TRUE AND LOWER(HEX(`category_translation`.`language_id`)) = :languageId
GROUP BY `category_translation`.`category_id`
ORDER BY `category_translation`.`name` ASC;
SQL;

                $categories = $this->connection->executeQuery($sql, ['languageId' => $languageId])->fetchAll(FetchMode::ASSOCIATIVE);

                $entity->setData(new GeneralStruct([
                    'countries' => $countries ?: null,
                    'tags' => $tags ?: null,
                    'categories' => $categories ?: null,
                    'productManufacturers' => $productManufacturers ?: null
                ]));
            }
        }
    }
}
