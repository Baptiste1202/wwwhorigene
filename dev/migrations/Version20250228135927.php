<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228135927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE drug_resistance_on_strain (id INT AUTO_INCREMENT NOT NULL, concentration INT NOT NULL, description VARCHAR(255) DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, drug_resistance_id INT DEFAULT NULL, strain_id INT DEFAULT NULL, INDEX IDX_623B5112A3654322 (drug_resistance_id), INDEX IDX_623B511269B9E007 (strain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE drug_resistance_on_strain ADD CONSTRAINT FK_623B5112A3654322 FOREIGN KEY (drug_resistance_id) REFERENCES drug_resistance (id)');
        $this->addSql('ALTER TABLE drug_resistance_on_strain ADD CONSTRAINT FK_623B511269B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_drug_resistance DROP FOREIGN KEY FK_AA09A63EA3654322');
        $this->addSql('ALTER TABLE strain_drug_resistance DROP FOREIGN KEY FK_AA09A63E69B9E007');
        $this->addSql('DROP TABLE strain_drug_resistance');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE strain_drug_resistance (strain_id INT NOT NULL, drug_resistance_id INT NOT NULL, INDEX IDX_AA09A63EA3654322 (drug_resistance_id), INDEX IDX_AA09A63E69B9E007 (strain_id), PRIMARY KEY(strain_id, drug_resistance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE strain_drug_resistance ADD CONSTRAINT FK_AA09A63EA3654322 FOREIGN KEY (drug_resistance_id) REFERENCES drug_resistance (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain_drug_resistance ADD CONSTRAINT FK_AA09A63E69B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE drug_resistance_on_strain DROP FOREIGN KEY FK_623B5112A3654322');
        $this->addSql('ALTER TABLE drug_resistance_on_strain DROP FOREIGN KEY FK_623B511269B9E007');
        $this->addSql('DROP TABLE drug_resistance_on_strain');
    }
}
