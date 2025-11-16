<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116124955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne
        $this->addSql('ALTER TABLE sample ADD COLUMN user_id INT');

        // Ajouter la contrainte de clé étrangère
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT fk_sample_user FOREIGN KEY (user_id) REFERENCES "user"(id)');

        // Créer l’index
        $this->addSql('CREATE INDEX idx_sample_user_id ON sample (user_id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la clé étrangère
        $this->addSql('ALTER TABLE sample DROP CONSTRAINT fk_sample_user');

        // Supprimer l’index
        $this->addSql('DROP INDEX idx_sample_user_id');

        // Supprimer la colonne
        $this->addSql('ALTER TABLE sample DROP COLUMN user_id');
    }
}

