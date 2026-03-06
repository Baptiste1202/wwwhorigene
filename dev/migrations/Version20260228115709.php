<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260228115709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration adapted for PostgreSQL: types and rename syntax.';
    }

    public function up(Schema $schema): void
    {
        // 1. Utilisation de TEXT au lieu de LONGTEXT
        $this->addSql('ALTER TABLE method_sequencing_type ADD description TEXT DEFAULT NULL, ADD comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE phenotype_type ADD description TEXT DEFAULT NULL, ADD comment TEXT DEFAULT NULL');
        
        // 2. Ajout des colonnes VARCHAR (compatible)
        $this->addSql('ALTER TABLE sample ADD bio_sample VARCHAR(255) DEFAULT NULL, ADD farm_location VARCHAR(255) DEFAULT NULL, ADD hospital_sample_type VARCHAR(255) DEFAULT NULL, ADD hospital_site VARCHAR(255) DEFAULT NULL, ADD hospital_ward VARCHAR(255) DEFAULT NULL, ADD patient_context_type VARCHAR(255) DEFAULT NULL, ADD source VARCHAR(255) DEFAULT NULL');
        
        // 3. Syntaxe de renommage spécifique à PostgreSQL
        $this->addSql('ALTER TABLE strain RENAME COLUMN info_genotype TO accession_number');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE method_sequencing_type DROP description, DROP comment');
        $this->addSql('ALTER TABLE phenotype_type DROP description, DROP comment');
        $this->addSql('ALTER TABLE sample DROP bio_sample, DROP farm_location, DROP hospital_sample_type, DROP hospital_site, DROP hospital_ward, DROP patient_context_type, DROP source');
        
        // Inversion du renommage
        $this->addSql('ALTER TABLE strain RENAME COLUMN accession_number TO info_genotype');
    }
}
