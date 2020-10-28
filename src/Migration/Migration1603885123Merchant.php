<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1603885123Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1603885123;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_translation` (
    `moorl_merchant_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    `name` varchar(255),
    `opening_hours` longtext DEFAULT NULL,
    `description` longtext DEFAULT NULL,
    `description_html` longtext DEFAULT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`moorl_merchant_id`, `language_id`),
    CONSTRAINT `fk.moorl_merchant_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.moorl_merchant_translation.moorl_merchant_id` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE     
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
