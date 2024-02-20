<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1565395385Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1_565_395_385;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant` (
    `id` BINARY(16) NOT NULL,
    `sales_channel_id` BINARY(16),
    `country_id` BINARY(16),
    `customer_group_id` BINARY(16),
    `media_id` BINARY(16),
    `marker_id` BINARY(16),
    `marker_shadow_id` BINARY(16),
    `product_manufacturer_id` BINARY(16),
    `origin_id` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `first_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `last_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `active` TINYINT,
    `zipcode` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `city` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `company` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `street` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `street_number` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `country` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `country_code` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `location_lat` DOUBLE,
    `location_lon` DOUBLE,
    `department` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `vat_id` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `phone_number` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `data` JSON,
    `additional_address_line1` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `additional_address_line2` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `shop_url` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `merchant_url` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `description` TEXT COLLATE utf8mb4_unicode_ci,
    `marker_settings` TEXT COLLATE utf8mb4_unicode_ci,
    `opening_hours` TEXT COLLATE utf8mb4_unicode_ci,
    `custom_fields` JSON,
    `auto_increment` INT(11),
    `created_at` DATETIME(3),
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_zipcode` (
    `id` VARCHAR(255) PRIMARY KEY,
    `zipcode` VARCHAR(255), 
    `suburb` VARCHAR(255),
    `city` VARCHAR(255) COLLATE utf8mb4_unicode_ci, 
    `country_code` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `country` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `state` VARCHAR(255) COLLATE utf8mb4_unicode_ci, 
    `lon` DOUBLE, 
    `lat` DOUBLE,
    `licence` VARCHAR(255) COLLATE utf8mb4_unicode_ci 
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
