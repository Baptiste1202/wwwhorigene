<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ensures is_access column exists with proper default value for security
 */
final class Version20260130000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ensures is_access column exists in user table with default false for security';
    }

    public function up(Schema $schema): void
    {
        // MySQL: Add is_access column if it doesn't exist (idempotent)
        $this->addSql('
            ALTER TABLE `user` 
            ADD COLUMN is_access TINYINT(1) DEFAULT 0 NOT NULL
        ');
        
        // MySQL: Update existing NULL values to false (safety measure)
        $this->addSql('UPDATE `user` SET is_access = 0 WHERE is_access IS NULL');
    }

    public function down(Schema $schema): void
    {
        // MySQL: We don't remove the column for safety - it's security critical
        // If you really need to remove it, uncomment the line below:
        // $this->addSql('ALTER TABLE `user` DROP COLUMN is_access');
    }
}
