<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1662306139 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1662306139;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant`
ADD `salutation_id` BINARY(16) NULL,
ADD `visible` TINYINT(4) NOT NULL DEFAULT '1',
ADD `moorl_marker_id` BINARY(16) NULL,
ADD `country_state_id` BINARY(16) NULL,
ADD `location_place_id` VARCHAR(255) NULL,
ADD `location_data` JSON NULL;
SQL;
        $connection->executeUpdate($sql);

        $sql = <<<SQL
ALTER TABLE `moorl_merchant_translation`
ADD `slot_config` JSON,
ADD `teaser` longtext COLLATE 'utf8mb4_unicode_ci' NULL,
ADD `meta_title` longtext COLLATE 'utf8mb4_unicode_ci' NULL,
ADD `meta_description` longtext COLLATE 'utf8mb4_unicode_ci' NULL;
SQL;
        $connection->executeUpdate($sql);

        $sql = <<<SQL
UPDATE `moorl_merchant_translation` 
SET 
    `teaser` = `description`,
    `description` = `description_html`;
SQL;
        $connection->executeUpdate($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
