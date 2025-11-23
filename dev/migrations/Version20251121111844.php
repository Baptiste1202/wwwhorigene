<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251121111844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // PostgreSQL syntax
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date TYPE TIMESTAMP USING date::timestamp');
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date DROP NOT NULL');

        // Add new user_id column
        $this->addSql('ALTER TABLE sample ADD COLUMN user_id INT DEFAULT NULL');

        // Drop old "user" column
        $this->addSql('ALTER TABLE sample DROP COLUMN user');

        // Foreign key creation
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Index creation
        $this->addSql('CREATE INDEX IDX_F10B76C3A76ED395 ON sample (user_id)');
    }

    public function down(Schema $schema): void
    {
        // Revert drug_resistance_on_strain.date to VARCHAR
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN date TYPE VARCHAR(255) USING date::varchar');

        // Foreign key
        $this->addSql('ALTER TABLE sample DROP CONSTRAINT FK_F10B76C3A76ED395');

        // Drop index
        $this->addSql('DROP INDEX IDX_F10B76C3A76ED395');

        // Restore old user column
        $this->addSql('ALTER TABLE sample ADD COLUMN user VARCHAR(255) NOT NULL');

        // Remove user_id
        $this->addSql('ALTER TABLE sample DROP COLUMN user_id');
    }
}
