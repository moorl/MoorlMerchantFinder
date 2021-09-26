<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1631631689 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1631631689;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant`
DROP `opening_hours`,
ADD `is_default` tinyint(4) NULL AFTER `active`,
ADD `delivery_active` tinyint(4) NULL AFTER `active`,
ADD `collect_active` tinyint(4) NULL AFTER `active`,
ADD `auto_location` tinyint(4) NULL AFTER `active`,
ADD `opening_hours` json NULL AFTER `marker_settings`,
ADD `time_zone` varchar(255) NOT NULL DEFAULT 'Europe/Berlin' AFTER `marker_settings`,
ADD `delivery_type` varchar(255) NOT NULL DEFAULT 'radius' AFTER `marker_settings`,
ADD `delivery_price` FLOAT NOT NULL DEFAULT 2 AFTER `marker_settings`,
ADD `min_order_value` FLOAT NOT NULL DEFAULT 20 AFTER `marker_settings`,
ADD `tax_number` varchar(255) AFTER `marker_settings`,
ADD `tax_office` varchar(255) AFTER `marker_settings`,
ADD `bank_name` varchar(255) AFTER `marker_settings`,
ADD `bank_iban` varchar(255) AFTER `marker_settings`,
ADD `bank_bic` varchar(255) AFTER `marker_settings`,
ADD `place_of_jurisdiction` varchar(255) AFTER `marker_settings`,
ADD `place_of_fulfillment` varchar(255) AFTER `marker_settings`,
ADD `executive_director` varchar(255) AFTER `marker_settings`;
SQL;
        $connection->executeUpdate($sql);

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_area` (
    `id` BINARY(16) NOT NULL,
    `moorl_merchant_id` BINARY(16) NOT NULL,
    `zipcode` VARCHAR(255) NULL,
    `delivery_price` FLOAT NOT NULL DEFAULT 2,
    `min_order_value` FLOAT NOT NULL DEFAULT 20,
    `delivery_time` INT(11),
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),

    PRIMARY KEY (`id`),

    CONSTRAINT `fk.dewa_merchant_area.moorl_merchant_id` 
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
