<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1686822866 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1686822866;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant_product_manufacturer`
DROP FOREIGN KEY `fk.moorl_merchant_product_manufacturer.product_manufacturer_id`;
    
ALTER TABLE `moorl_merchant_product_manufacturer`
ADD CONSTRAINT `fk.moorl_merchant_product_manufacturer.product_manufacturer_id` 
    FOREIGN KEY (`product_manufacturer_id`) 
    REFERENCES `product_manufacturer` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
