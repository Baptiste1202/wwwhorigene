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
    // PostgreSQL : DROP CONSTRAINT au lieu de DROP FOREIGN KEY
    $this->addSql('ALTER TABLE strain DROP CONSTRAINT FK_A630CD728510D4DE');
    
    $this->addSql('ALTER TABLE strain DROP COLUMN depot_id');
    $this->addSql('DROP TABLE depot');
    }

    public function down(Schema $schema): void
    {
    // PostgreSQL : SERIAL au lieu de INT AUTO_INCREMENT, et pas de ENGINE
    $this->addSql('CREATE TABLE depot (id SERIAL NOT NULL, date_depot DATE NOT NULL, type VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, "user" VARCHAR(255) NOT NULL, sample VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    
    $this->addSql('ALTER TABLE strain ADD depot_id INT DEFAULT NULL');
    
    // La FK reste standard, mais attention aux guillemets si nécessaire
    $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD728510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    $this->addSql('CREATE INDEX IDX_A630CD728510D4DE ON strain (depot_id)');
    }
}
