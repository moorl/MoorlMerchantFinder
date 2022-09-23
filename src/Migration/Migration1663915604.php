<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Moorl\MerchantFinder\MoorlMerchantFinder;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663915604 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1663915604;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant_stock`
ADD `available_stock` int(11) NOT NULL
ADD `sales` INT(11) NOT NULL;
SQL;
        $connection->executeStatement($sql);

        foreach (MoorlMerchantFinder::INHERITANCES as $table => $propertyNames) {
            foreach ($propertyNames as $propertyName) {
                try {
                    $this->updateInheritance($connection, $table, $propertyName);
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
