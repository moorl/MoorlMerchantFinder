<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1610207656 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1610207656;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant`
ADD `moorl_merchant_marker_id` BINARY(16) NULL AFTER `highlight`;
SQL;

        $connection->executeUpdate($sql);

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_marker` (
    `id` BINARY(16) NOT NULL,
    `marker_id` BINARY(16),
    `marker_retina_id` BINARY(16),
    `marker_shadow_id` BINARY(16),
    `marker_settings` JSON,
    `name` varchar(255),
    `type` varchar(255),
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeUpdate($sql);

        $sql = <<<SQL
SET NAMES utf8mb4;

INSERT INTO `moorl_merchant_marker` (`id`, `marker_settings`, `name`, `created_at`) VALUES
(UNHEX('15CB2C4BF050495CA3DE2419F0DAB744'),	'{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Green', '2021-01-09 17:23:06.389'),
(UNHEX('4C015DF79FD643D8AEB335106DCFA63D'),	'{\"iconSizeX\": 38, \"iconSizeY\": 95, \"iconAnchorX\": 22, \"iconAnchorY\": 94, \"shadowSizeX\": 50, \"shadowSizeY\": 64, \"popupAnchorX\": -3, \"popupAnchorY\": -76, \"shadowAnchorX\": 4, \"shadowAnchorY\": 62}',	'Leaf Orange', '2021-01-09 17:16:00.841'),
(UNHEX('59CC730726ED4B19876DCB6FE611F486'),	'{\"iconSizeX\": 38, \"iconSizeY\": 95, \"iconAnchorX\": 22, \"iconAnchorY\": 94, \"shadowSizeX\": 50, \"shadowSizeY\": 64, \"popupAnchorX\": -3, \"popupAnchorY\": -76, \"shadowAnchorX\": 4, \"shadowAnchorY\": 62}',	'Leaf Green', '2021-01-09 17:16:00.841'),
(UNHEX('808D379A499448B6A8F42BFD6ACB89E3'),	'{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Gold',	'2021-01-09 17:23:06.389'),
(UNHEX('9C24E4AC76234132BB1BF8255205EAFF'),	'{\"iconSizeX\": 38, \"iconSizeY\": 95, \"iconAnchorX\": 22, \"iconAnchorY\": 94, \"shadowSizeX\": 50, \"shadowSizeY\": 64, \"popupAnchorX\": -3, \"popupAnchorY\": -76, \"shadowAnchorX\": 4, \"shadowAnchorY\": 62}',	'Leaf Red',	'2021-01-09 17:16:00.841'),
(UNHEX('9D88C423DC244F7B98547FB386518CF8'), '{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Blue',	'2021-01-09 17:23:06.389'),
(UNHEX('9F74822D15B74063BC807CB89E518498'),	'{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Purple', '2021-01-09 17:23:06.389'),
(UNHEX('C1AB4C5E30544F38B9F73EF975B5A48D'),	'{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Orange', '2021-01-09 17:23:06.389'),
(UNHEX('E60EA6DD7BAC4401AFBBF8AE388B9DD0'),	'{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Green', '2021-01-09 17:23:06.389'),
(UNHEX('FB3E870BB8AF468C983A02BFCB3F67F2'),	'{\"iconSizeX\": 25, \"iconSizeY\": 41, \"iconAnchorX\": 12, \"iconAnchorY\": 41, \"shadowSizeX\": 41, \"shadowSizeY\": 41, \"popupAnchorX\": 1, \"popupAnchorY\": -34, \"shadowAnchorX\": 6, \"shadowAnchorY\": 21}',	'Red', '2021-01-09 17:23:06.389');
SQL;

        //$connection->executeUpdate($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}