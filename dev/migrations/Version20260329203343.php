<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329203343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'P0/P1 cleanup: rename columns safely and preserve data (PostgreSQL version)';
    }

    public function up(Schema $schema): void
    {
        // 1. drug_resistance_on_strain.concentration : int -> float (DOUBLE PRECISION)
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER concentration TYPE DOUBLE PRECISION');

        // 2. phenotype.mesure -> phenotype.measure
        $this->addSql('ALTER TABLE phenotype RENAME COLUMN mesure TO measure');

        // 3. publication : autor -> author / year : varchar -> int
        $this->addSql('ALTER TABLE publication RENAME COLUMN autor TO author');
        // Utilisation de USING pour convertir proprement le texte en entier
        $this->addSql('ALTER TABLE publication ALTER COLUMN year TYPE INT USING year::integer');

        // 4. sample : localisation -> location / under_localisation -> under_location
        $this->addSql('ALTER TABLE sample RENAME COLUMN localisation TO location');
        $this->addSql('ALTER TABLE sample RENAME COLUMN under_localisation TO under_location');

        // 5. sequencing : name_id -> method_sequencing_type_id
        // En PostgreSQL, on drop la constraint par son nom, pas besoin de spécifier "FOREIGN KEY"
        $this->addSql('ALTER TABLE sequencing DROP CONSTRAINT FK_EACB1D171179CD6');
        $this->addSql('DROP INDEX IDX_EACB1D171179CD6');
        $this->addSql('ALTER TABLE sequencing RENAME COLUMN name_id TO method_sequencing_type_id');
        $this->addSql('CREATE INDEX IDX_EACB1D1450F8A42 ON sequencing (method_sequencing_type_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D1450F8A42 FOREIGN KEY (method_sequencing_type_id) REFERENCES method_sequencing_type (id)');
    }

    public function down(Schema $schema): void
    {
        // Reverse sequencing change
        $this->addSql('ALTER TABLE sequencing DROP CONSTRAINT FK_EACB1D1450F8A42');
        $this->addSql('DROP INDEX IDX_EACB1D1450F8A42');
        $this->addSql('ALTER TABLE sequencing RENAME COLUMN method_sequencing_type_id TO name_id');
        $this->addSql('CREATE INDEX IDX_EACB1D171179CD6 ON sequencing (name_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D171179CD6 FOREIGN KEY (name_id) REFERENCES method_sequencing_type (id)');

        // Reverse sample
        $this->addSql('ALTER TABLE sample RENAME COLUMN location TO localisation');
        $this->addSql('ALTER TABLE sample RENAME COLUMN under_location TO under_localisation');

        // Reverse publication
        $this->addSql('ALTER TABLE publication RENAME COLUMN author TO autor');
        $this->addSql('ALTER TABLE publication ALTER COLUMN year TYPE VARCHAR(255) USING year::varchar');

        // Reverse phenotype
        $this->addSql('ALTER TABLE phenotype RENAME COLUMN measure TO mesure');

        // Reverse drug_resistance_on_strain
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER concentration TYPE INT');
    }
}
