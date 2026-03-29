<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329203343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'P0/P1 cleanup: rename columns safely and preserve data';
    }

    public function up(Schema $schema): void
    {
        // drug_resistance_on_strain.concentration : int -> float
        $this->addSql('ALTER TABLE drug_resistance_on_strain CHANGE concentration concentration DOUBLE PRECISION DEFAULT NULL');

        // phenotype.mesure -> phenotype.measure
        $this->addSql('ALTER TABLE phenotype CHANGE mesure measure VARCHAR(255) DEFAULT NULL');

        // publication.autor -> publication.author
        // publication.year : varchar -> int
        $this->addSql('ALTER TABLE publication CHANGE autor author VARCHAR(255) NOT NULL, CHANGE year year INT DEFAULT NULL');

        // sample.localisation -> sample.location
        // sample.under_localisation -> sample.under_location
        $this->addSql('ALTER TABLE sample CHANGE localisation location VARCHAR(255) DEFAULT NULL, CHANGE under_localisation under_location VARCHAR(255) DEFAULT NULL');

        // sequencing.name_id -> sequencing.method_sequencing_type_id
        // Keep data, fix FK and index
        $this->addSql('ALTER TABLE sequencing DROP FOREIGN KEY FK_EACB1D171179CD6');
        $this->addSql('DROP INDEX IDX_EACB1D171179CD6 ON sequencing');
        $this->addSql('ALTER TABLE sequencing CHANGE name_id method_sequencing_type_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_EACB1D1450F8A42 ON sequencing (method_sequencing_type_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D1450F8A42 FOREIGN KEY (method_sequencing_type_id) REFERENCES method_sequencing_type (id)');
    }

    public function down(Schema $schema): void
    {
        // sequencing.method_sequencing_type_id -> sequencing.name_id
        $this->addSql('ALTER TABLE sequencing DROP FOREIGN KEY FK_EACB1D1450F8A42');
        $this->addSql('DROP INDEX IDX_EACB1D1450F8A42 ON sequencing');
        $this->addSql('ALTER TABLE sequencing CHANGE method_sequencing_type_id name_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_EACB1D171179CD6 ON sequencing (name_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D171179CD6 FOREIGN KEY (name_id) REFERENCES method_sequencing_type (id)');

        // sample.location -> sample.localisation
        // sample.under_location -> sample.under_localisation
        $this->addSql('ALTER TABLE sample CHANGE location localisation VARCHAR(255) DEFAULT NULL, CHANGE under_location under_localisation VARCHAR(255) DEFAULT NULL');

        // publication.author -> publication.autor
        // publication.year : int -> varchar
        $this->addSql('ALTER TABLE publication CHANGE author autor VARCHAR(255) NOT NULL, CHANGE year year VARCHAR(255) NOT NULL');

        // phenotype.measure -> phenotype.mesure
        $this->addSql('ALTER TABLE phenotype CHANGE measure mesure VARCHAR(255) DEFAULT NULL');

        // drug_resistance_on_strain.concentration : float -> int
        $this->addSql('ALTER TABLE drug_resistance_on_strain CHANGE concentration concentration INT DEFAULT NULL');
    }
}