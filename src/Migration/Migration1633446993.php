<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1633446993 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1633446993;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_sales_channel` (
    `sales_channel_id` BINARY(16) NOT NULL,
    `moorl_merchant_id` BINARY(16) NOT NULL,

    PRIMARY KEY (`sales_channel_id`, `moorl_merchant_id`),

    CONSTRAINT `fk.moorl_merchant_sales_channel.sales_channel_id` 
        FOREIGN KEY (`sales_channel_id`)
        REFERENCES `sales_channel` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.moorl_merchant_sales_channel.moorl_merchant_id` 
        FOREIGN KEY (`moorl_merchant_id`)
        REFERENCES `moorl_merchant` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeUpdate($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
