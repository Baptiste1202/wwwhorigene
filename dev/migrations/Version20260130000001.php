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
        // PostgreSQL: Check if column exists before adding (idempotent)
        $this->addSql('
            DO $$ 
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 
                    FROM information_schema.columns 
                    WHERE table_schema = \'public\'
                      AND table_name = \'user\' 
                      AND column_name = \'is_access\'
                ) THEN
                    ALTER TABLE "user" ADD COLUMN is_access BOOLEAN DEFAULT false NOT NULL;
                END IF;
            END $$;
        ');
        
        // PostgreSQL: Ensure default value is false for security (idempotent)
        $this->addSql('
            DO $$ 
            BEGIN
                IF EXISTS (
                    SELECT 1 
                    FROM information_schema.columns 
                    WHERE table_schema = \'public\'
                      AND table_name = \'user\' 
                      AND column_name = \'is_access\'
                ) THEN
                    ALTER TABLE "user" ALTER COLUMN is_access SET DEFAULT false;
                END IF;
            END $$;
        ');
        
        // PostgreSQL: Update existing NULL values to false (safety measure)
        $this->addSql('UPDATE "user" SET is_access = false WHERE is_access IS NULL');
    }

    public function down(Schema $schema): void
    {
        // PostgreSQL: We don't remove the column for safety - it's security critical
        // If you really need to remove it, uncomment the line below:
        // $this->addSql('ALTER TABLE "user" DROP COLUMN IF EXISTS is_access');
    }
}
