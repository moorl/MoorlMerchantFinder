<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1604593154 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1604593154;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_stock` (
    `id` BINARY(16) NOT NULL,
    `moorl_merchant_id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `delivery_time_id` BINARY(16),
    `is_stock` TINYINT NOT NULL DEFAULT 0,
    `stock` INT(11),
    `info` varchar(255),
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`),
    KEY `idx.moorl_merchant_stock.product_id` (`product_id`),
    KEY `idx.moorl_merchant_stock.moorl_merchant_id` (`moorl_merchant_id`),
    CONSTRAINT `fk.moorl_merchant_stock.product_id` FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.moorl_merchant_stock.moorl_merchant_id` FOREIGN KEY (`moorl_merchant_id`)
        REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE     
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeUpdate($sql);

        $this->updateInheritance($connection, 'product', 'MoorlMerchants'); // TODO: check ob notwendig
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}