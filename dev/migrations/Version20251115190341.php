<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251115190341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Modifier la colonne 'date' de drug_resistance_on_strain avec conversion explicite
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date TYPE TIMESTAMP USING date::timestamp');
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date DROP DEFAULT');

        // Modifier la colonne 'is_verified' de user en 'is_access' de type BOOLEAN
        $this->addSql('ALTER TABLE "user" ALTER COLUMN is_verified TYPE BOOLEAN USING is_verified::boolean');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN is_verified TO is_access');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN is_access SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Revenir sur la colonne 'date' avec conversion explicite
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date TYPE VARCHAR(255) USING date::varchar');
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date DROP DEFAULT');

        // Revenir sur la colonne 'is_access' vers 'is_verified' de type BOOLEAN
        $this->addSql('ALTER TABLE "user" ALTER COLUMN is_access TYPE BOOLEAN USING is_access::boolean');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN is_access TO is_verified');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN is_verified SET NOT NULL');
    }
}
