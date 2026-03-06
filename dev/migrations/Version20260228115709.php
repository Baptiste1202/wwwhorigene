<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260228115709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE method_sequencing_type ADD description LONGTEXT DEFAULT NULL, ADD comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE phenotype_type ADD description LONGTEXT DEFAULT NULL, ADD comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE sample ADD bio_sample VARCHAR(255) DEFAULT NULL, ADD farm_location VARCHAR(255) DEFAULT NULL, ADD hospital_sample_type VARCHAR(255) DEFAULT NULL, ADD hospital_site VARCHAR(255) DEFAULT NULL, ADD hospital_ward VARCHAR(255) DEFAULT NULL, ADD patient_context_type VARCHAR(255) DEFAULT NULL, ADD source VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE strain CHANGE info_genotype accession_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE method_sequencing_type DROP description, DROP comment');
        $this->addSql('ALTER TABLE phenotype_type DROP description, DROP comment');
        $this->addSql('ALTER TABLE sample DROP bio_sample, DROP farm_location, DROP hospital_sample_type, DROP hospital_site, DROP hospital_ward, DROP patient_context_type, DROP source');
        $this->addSql('ALTER TABLE strain CHANGE accession_number info_genotype VARCHAR(255) DEFAULT NULL');
    }
}
