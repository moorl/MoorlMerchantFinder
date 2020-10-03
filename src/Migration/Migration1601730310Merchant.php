<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1601730310Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1601730310;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_oh` (
    `id` BINARY(16) NOT NULL,
    `moorl_merchant_id` BINARY(16),
    `repeat` TINYINT,
    `date` DATE,
    `show_from` DATE,
    `show_until` DATE,
    `opening_hours` JSON,
    `timetable` JSON,
    `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `created_at` DATETIME(3),
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
