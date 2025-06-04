<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221100045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collec (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, date_depot DATE NOT NULL, type VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, user VARCHAR(255) NOT NULL, sample VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE drug_resistance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, concentration INT NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE file_sequencing (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE genotype (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE method_sequencing (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plasmyd (id INT AUTO_INCREMENT NOT NULL, name_plasmyd VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, article_url VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, autor VARCHAR(255) NOT NULL, year VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sample (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, localisation VARCHAR(255) DEFAULT NULL, under_localisation VARCHAR(255) DEFAULT NULL, gps VARCHAR(255) DEFAULT NULL, environment VARCHAR(255) DEFAULT NULL, other VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, user VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, room VARCHAR(255) DEFAULT NULL, congelateur VARCHAR(255) DEFAULT NULL, etagere VARCHAR(255) DEFAULT NULL, rack VARCHAR(255) DEFAULT NULL, type_conteneur VARCHAR(255) DEFAULT NULL, position_conteneur VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE strain (id INT AUTO_INCREMENT NOT NULL, name_strain VARCHAR(255) NOT NULL, specie VARCHAR(100) NOT NULL, gender VARCHAR(100) NOT NULL, comment VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, storage VARCHAR(255) DEFAULT NULL, created_by_name VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, room VARCHAR(255) DEFAULT NULL, fridge VARCHAR(255) DEFAULT NULL, shelf VARCHAR(255) DEFAULT NULL, rack VARCHAR(255) DEFAULT NULL, container_type VARCHAR(255) DEFAULT NULL, container_position VARCHAR(255) DEFAULT NULL, parent_strain_id INT DEFAULT NULL, genotype_id INT DEFAULT NULL, project_id INT DEFAULT NULL, collection_id INT DEFAULT NULL, depot_id INT DEFAULT NULL, prelevement_id INT DEFAULT NULL, created_by_id INT NOT NULL, INDEX IDX_A630CD7298F2CFE9 (parent_strain_id), INDEX IDX_A630CD72E0EC46AF (genotype_id), INDEX IDX_A630CD72166D1F9C (project_id), INDEX IDX_A630CD72514956FD (collection_id), INDEX IDX_A630CD728510D4DE (depot_id), INDEX IDX_A630CD72CE389617 (prelevement_id), INDEX IDX_A630CD72B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE strain_transformability (strain_id INT NOT NULL, transformability_id INT NOT NULL, INDEX IDX_424257B469B9E007 (strain_id), INDEX IDX_424257B4108272A0 (transformability_id), PRIMARY KEY(strain_id, transformability_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE strain_plasmyd (strain_id INT NOT NULL, plasmyd_id INT NOT NULL, INDEX IDX_EB2D4A3669B9E007 (strain_id), INDEX IDX_EB2D4A363B91781 (plasmyd_id), PRIMARY KEY(strain_id, plasmyd_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE strain_drug_resistance (strain_id INT NOT NULL, drug_resistance_id INT NOT NULL, INDEX IDX_AA09A63E69B9E007 (strain_id), INDEX IDX_AA09A63EA3654322 (drug_resistance_id), PRIMARY KEY(strain_id, drug_resistance_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE strain_publication (strain_id INT NOT NULL, publication_id INT NOT NULL, INDEX IDX_AC80D2C569B9E007 (strain_id), INDEX IDX_AC80D2C538B217A7 (publication_id), PRIMARY KEY(strain_id, publication_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE strain_method_sequencing (strain_id INT NOT NULL, method_sequencing_id INT NOT NULL, INDEX IDX_3C72C8A869B9E007 (strain_id), INDEX IDX_3C72C8A85D0538B2 (method_sequencing_id), PRIMARY KEY(strain_id, method_sequencing_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE transformability (id INT AUTO_INCREMENT NOT NULL, technique VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, mesure VARCHAR(255) DEFAULT NULL, type_fichier VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(180) NOT NULL, lastname VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD7298F2CFE9 FOREIGN KEY (parent_strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72E0EC46AF FOREIGN KEY (genotype_id) REFERENCES genotype (id)');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72514956FD FOREIGN KEY (collection_id) REFERENCES collec (id)');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD728510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id)');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72CE389617 FOREIGN KEY (prelevement_id) REFERENCES sample (id)');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE strain_transformability ADD CONSTRAINT FK_424257B469B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_transformability ADD CONSTRAINT FK_424257B4108272A0 FOREIGN KEY (transformability_id) REFERENCES transformability (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain_plasmyd ADD CONSTRAINT FK_EB2D4A3669B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_plasmyd ADD CONSTRAINT FK_EB2D4A363B91781 FOREIGN KEY (plasmyd_id) REFERENCES plasmyd (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain_drug_resistance ADD CONSTRAINT FK_AA09A63E69B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_drug_resistance ADD CONSTRAINT FK_AA09A63EA3654322 FOREIGN KEY (drug_resistance_id) REFERENCES drug_resistance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain_publication ADD CONSTRAINT FK_AC80D2C569B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_publication ADD CONSTRAINT FK_AC80D2C538B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain_method_sequencing ADD CONSTRAINT FK_3C72C8A869B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_method_sequencing ADD CONSTRAINT FK_3C72C8A85D0538B2 FOREIGN KEY (method_sequencing_id) REFERENCES method_sequencing (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD7298F2CFE9');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72E0EC46AF');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72166D1F9C');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72514956FD');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD728510D4DE');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72CE389617');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72B03A8386');
        $this->addSql('ALTER TABLE strain_transformability DROP FOREIGN KEY FK_424257B469B9E007');
        $this->addSql('ALTER TABLE strain_transformability DROP FOREIGN KEY FK_424257B4108272A0');
        $this->addSql('ALTER TABLE strain_plasmyd DROP FOREIGN KEY FK_EB2D4A3669B9E007');
        $this->addSql('ALTER TABLE strain_plasmyd DROP FOREIGN KEY FK_EB2D4A363B91781');
        $this->addSql('ALTER TABLE strain_drug_resistance DROP FOREIGN KEY FK_AA09A63E69B9E007');
        $this->addSql('ALTER TABLE strain_drug_resistance DROP FOREIGN KEY FK_AA09A63EA3654322');
        $this->addSql('ALTER TABLE strain_publication DROP FOREIGN KEY FK_AC80D2C569B9E007');
        $this->addSql('ALTER TABLE strain_publication DROP FOREIGN KEY FK_AC80D2C538B217A7');
        $this->addSql('ALTER TABLE strain_method_sequencing DROP FOREIGN KEY FK_3C72C8A869B9E007');
        $this->addSql('ALTER TABLE strain_method_sequencing DROP FOREIGN KEY FK_3C72C8A85D0538B2');
        $this->addSql('DROP TABLE collec');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE drug_resistance');
        $this->addSql('DROP TABLE file_sequencing');
        $this->addSql('DROP TABLE genotype');
        $this->addSql('DROP TABLE method_sequencing');
        $this->addSql('DROP TABLE plasmyd');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE sample');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE strain');
        $this->addSql('DROP TABLE strain_transformability');
        $this->addSql('DROP TABLE strain_plasmyd');
        $this->addSql('DROP TABLE strain_drug_resistance');
        $this->addSql('DROP TABLE strain_publication');
        $this->addSql('DROP TABLE strain_method_sequencing');
        $this->addSql('DROP TABLE transformability');
        $this->addSql('DROP TABLE user');
    }
}
