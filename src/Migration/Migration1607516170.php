<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1607516170 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1_607_516_170;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
DROP TABLE `moorl_merchant_customer`;
SQL;
        $connection->executeStatement($sql);

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_customer` (
    `id` BINARY(16) NOT NULL,
    `moorl_merchant_id` BINARY(16) NOT NULL,
    `customer_id` BINARY(16) NOT NULL,
    
    `customer_number` varchar(255),
    `info` varchar(255),
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq.moorl_merchant_customer.moorl_merchant_customer_id` (`moorl_merchant_id`, `customer_id`),
    
    KEY `idx.moorl_merchant_customer.customer_id` (`customer_id`),
    KEY `idx.moorl_merchant_customer.moorl_merchant_id` (`moorl_merchant_id`),
    
    CONSTRAINT `fk.moorl_merchant_customer.customer_id` FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.moorl_merchant_customer.moorl_merchant_id` FOREIGN KEY (`moorl_merchant_id`)
        REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE     
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}