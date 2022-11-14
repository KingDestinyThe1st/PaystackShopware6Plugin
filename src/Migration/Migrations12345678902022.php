<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migrations12345678902022 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 12345678902022;
    }
    
    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `paystack_keys` (
    `id` BINARY(16) NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `public_key` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `secret_key` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `payment_method_id` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}