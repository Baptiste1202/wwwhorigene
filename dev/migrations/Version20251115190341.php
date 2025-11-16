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
    }

    public function down(Schema $schema): void
    {
        // Revenir sur la colonne 'date' avec conversion explicite
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date TYPE VARCHAR(255) USING date::varchar');
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date DROP DEFAULT');
    }
}
