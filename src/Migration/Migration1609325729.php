<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1609325729 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1_609_325_729;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant`
ADD `type` varchar(255) NULL AFTER `highlight`,
ADD `custom1` varchar(255) NULL AFTER `type`,
ADD `custom2` varchar(255) NULL AFTER `custom1`,
ADD `custom3` varchar(255) NULL AFTER `custom2`,
ADD `custom4` varchar(255) NULL AFTER `custom3`;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}