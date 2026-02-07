<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109184642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds size_file column (BIGINT) to method_sequencing table';
    }

    public function up(Schema $schema): void
    {
        // MySQL syntax: Using BIGINT for file sizes > 2GB
        $this->addSql('ALTER TABLE method_sequencing ADD COLUMN size_file BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // MySQL syntax: Clean drop
        $this->addSql('ALTER TABLE method_sequencing DROP COLUMN size_file');
    }
}