<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1584635124Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1584635124;
    }

    public function update(Connection $connection): void
    {

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `moorl_merchant_category` (
  `moorl_merchant_id` binary(16) NOT NULL,
  `category_id` binary(16) NOT NULL,
  PRIMARY KEY (`moorl_merchant_id`,`category_id`),
  KEY `fk.moorl_merchant_category.category_id` (`category_id`),
  CONSTRAINT `fk.moorl_merchant_category.moorl_merchant_id` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`),
  CONSTRAINT `fk.moorl_merchant_category.category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `moorl_merchant_tag` (
  `moorl_merchant_id` binary(16) NOT NULL,
  `tag_id` binary(16) NOT NULL,
  PRIMARY KEY (`moorl_merchant_id`,`tag_id`),
  KEY `fk.moorl_merchant_tag.tag_id` (`tag_id`),
  CONSTRAINT `fk.moorl_merchant_tag.moorl_merchant_id` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`),
  CONSTRAINT `fk.moorl_merchant_tag.tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `moorl_merchant_product_manufacturer` (
  `moorl_merchant_id` binary(16) NOT NULL,
  `product_manufacturer_id` binary(16) NOT NULL,
  PRIMARY KEY (`moorl_merchant_id`,`product_manufacturer_id`),
  KEY `fk.moorl_merchant_product_manufacturer.product_manufacturer_id` (`product_manufacturer_id`),
  CONSTRAINT `fk.moorl_merchant_product_manufacturer.moorl_merchant_id` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`),
  CONSTRAINT `fk.moorl_merchant_product_manufacturer.product_manufacturer_id` FOREIGN KEY (`product_manufacturer_id`) REFERENCES `product_manufacturer` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeQuery($sql);

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
