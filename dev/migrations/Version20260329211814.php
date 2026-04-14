<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329211814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align signed integers for sequencing and method_sequencing_type (PostgreSQL version)';
    }

    public function up(Schema $schema): void
    {
        // 1. Suppression de la contrainte et de l'index
        $this->addSql('ALTER TABLE sequencing DROP CONSTRAINT FK_EACB1D1450F8A42');
        $this->addSql('DROP INDEX IDX_EACB1D1450F8A42');

        // 2. Alignement des types pour method_sequencing_type
        // Note: PostgreSQL gère l'auto-incrément via des séquences ou IDENTITY. 
        // Si la colonne est déjà une clé primaire, on change juste le type de données.
        $this->addSql('ALTER TABLE method_sequencing_type ALTER COLUMN id TYPE INT');

        // 3. Alignement des types pour sequencing
        $this->addSql('ALTER TABLE sequencing ALTER COLUMN id TYPE INT');
        $this->addSql('ALTER TABLE sequencing ALTER COLUMN method_sequencing_type_id TYPE INT');

        // 4. Nettoyage d'index (PostgreSQL ne crée pas d'index auto nommé 'id' sur les PK, 
        // mais si un index manuel existe, on le retire par son nom)
        $this->addSql('DROP INDEX IF EXISTS id'); 

        // 5. Recréation de l'index et de la FK
        $this->addSql('CREATE INDEX IDX_EACB1D1450F8A42 ON sequencing (method_sequencing_type_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D1450F8A42 FOREIGN KEY (method_sequencing_type_id) REFERENCES method_sequencing_type (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sequencing DROP CONSTRAINT FK_EACB1D1450F8A42');
        $this->addSql('DROP INDEX IDX_EACB1D1450F8A42');

        // Retour vers INT (car UNSIGNED n'existe pas en natif PG, on reste sur du INT standard)
        $this->addSql('ALTER TABLE sequencing ALTER COLUMN id TYPE INT');
        $this->addSql('ALTER TABLE sequencing ALTER COLUMN method_sequencing_type_id TYPE INT');
        $this->addSql('ALTER TABLE method_sequencing_type ALTER COLUMN id TYPE INT');

        $this->addSql('CREATE INDEX IDX_EACB1D1450F8A42 ON sequencing (method_sequencing_type_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D1450F8A42 FOREIGN KEY (method_sequencing_type_id) REFERENCES method_sequencing_type (id)');
    }
}
