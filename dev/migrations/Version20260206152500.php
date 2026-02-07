<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove depot table and depot_id column from strain table
 */
final class Version20260206152500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove depot table and depot_id foreign key from strain';
    }

    public function up(Schema $schema): void
    {
        // Remove foreign key constraint first
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD728510D4DE');
        
        // Remove the depot_id column from strain table
        $this->addSql('ALTER TABLE strain DROP COLUMN depot_id');
        
        // Drop the depot table
        $this->addSql('DROP TABLE depot');
    }

    public function down(Schema $schema): void
    {
        // Recreate depot table
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, date_depot DATE NOT NULL, type VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, user VARCHAR(255) NOT NULL, sample VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Add depot_id column back to strain
        $this->addSql('ALTER TABLE strain ADD depot_id INT DEFAULT NULL');
        
        // Recreate the foreign key
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD728510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id)');
        $this->addSql('CREATE INDEX IDX_A630CD728510D4DE ON strain (depot_id)');
    }
}
