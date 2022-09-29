<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1588727980Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1588727980;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant`
ADD `priority` int(11) NULL AFTER `id`,
ADD `highlight` tinyint(4) NULL AFTER `priority`;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
