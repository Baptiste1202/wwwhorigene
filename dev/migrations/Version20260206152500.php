<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove depot table and depot_id column from strain table (PostgreSQL Compatible)
 */
final class Version20260206152500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove depot table and depot_id foreign key from strain (Safe execution)';
    }

    public function up(Schema $schema): void
    {
        // 1. Supprimer la contrainte SEULEMENT si elle existe (Syntaxe Postgres)
        // Note: DROP CONSTRAINT au lieu de DROP FOREIGN KEY
        $this->addSql('ALTER TABLE strain DROP CONSTRAINT IF EXISTS FK_A630CD728510D4DE');
        
        // 2. Supprimer l'index associé s'il existe (bonnes pratiques)
        $this->addSql('DROP INDEX IF EXISTS IDX_A630CD728510D4DE');
        
        // 3. Supprimer la colonne depot_id si elle existe
        $this->addSql('ALTER TABLE strain DROP COLUMN IF EXISTS depot_id');
        
        // 4. Supprimer la table depot si elle existe
        $this->addSql('DROP TABLE IF EXISTS depot');
    }

    public function down(Schema $schema): void
    {
        // Recreate depot table (Syntaxe Postgres : SERIAL, pas de ENGINE)
        $this->addSql('CREATE TABLE depot (id SERIAL NOT NULL, date_depot DATE NOT NULL, type VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, "user" VARCHAR(255) NOT NULL, sample VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        
        // Add depot_id column back to strain
        $this->addSql('ALTER TABLE strain ADD depot_id INT DEFAULT NULL');
        
        // Recreate the foreign key
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD728510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A630CD728510D4DE ON strain (depot_id)');
    }
}
