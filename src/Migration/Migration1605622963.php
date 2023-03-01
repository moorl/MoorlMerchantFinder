<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1605622963 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1_605_622_963;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_customer` (
    `moorl_merchant_id` binary(16) NOT NULL,
    `customer_id` binary(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    
    PRIMARY KEY (`moorl_merchant_id`,`customer_id`),
    
    CONSTRAINT `fk.moorl_merchant_customer.customer_id` 
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        
    CONSTRAINT `fk.moorl_merchant_customer.moorl_merchant_id` 
        FOREIGN KEY (`moorl_merchant_id`) 
        REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);

        //$this->updateInheritance($connection, 'product', 'MoorlMerchants'); // TODO: check ob notwendig
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}