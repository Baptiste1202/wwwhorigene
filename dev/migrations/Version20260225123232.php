<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225123232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add concentration_unit + is_favorite (PostgreSQL compatible)';
    }

    public function up(Schema $schema): void
    {
        // 1️⃣ Ajout des colonnes
        $this->addSql('ALTER TABLE drug_resistance_on_strain ADD concentration_unit VARCHAR(50) DEFAULT NULL');
        
        // Utilisation de BOOLEAN pour Postgres
        $this->addSql('ALTER TABLE strain ADD is_favorite BOOLEAN DEFAULT FALSE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Suppression des colonnes ajoutées dans le up()
        $this->addSql('ALTER TABLE drug_resistance_on_strain DROP concentration_unit');
        $this->addSql('ALTER TABLE strain DROP is_favorite');

        $this->addSql('ALTER TABLE method_sequencing_type ALTER COLUMN id TYPE INT');

        // Suppression de la FK (Syntaxe Postgres : DROP CONSTRAINT)
        $this->addSql('ALTER TABLE sample DROP CONSTRAINT IF EXISTS FK_F10B76C3A76ED395');
        $this->addSql('DROP INDEX IF EXISTS IDX_F10B76C3A76ED395');

        $this->addSql('ALTER TABLE sequencing ALTER COLUMN id TYPE INT');
        $this->addSql('ALTER TABLE sequencing ALTER COLUMN size_file TYPE BIGINT');
        $this->addSql('ALTER TABLE sequencing ALTER COLUMN name_id TYPE INT');
    }
}
