<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903154813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE phenotype (id INT AUTO_INCREMENT NOT NULL, technique VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, mesure VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, strain_id INT DEFAULT NULL, INDEX IDX_482C09B469B9E007 (strain_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE phenotype ADD CONSTRAINT FK_482C09B469B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE transformability DROP FOREIGN KEY FK_5AB058BE69B9E007');
        $this->addSql('DROP TABLE transformability');
        $this->addSql('ALTER TABLE strain_collec DROP FOREIGN KEY FK_3E5F3C8069B9E007');
        $this->addSql('ALTER TABLE strain_collec ADD CONSTRAINT FK_3E5F3C8069B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transformability (id INT AUTO_INCREMENT NOT NULL, technique VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, mesure VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, strain_id INT DEFAULT NULL, INDEX IDX_5AB058BE69B9E007 (strain_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE transformability ADD CONSTRAINT FK_5AB058BE69B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE phenotype DROP FOREIGN KEY FK_482C09B469B9E007');
        $this->addSql('DROP TABLE phenotype');
        $this->addSql('ALTER TABLE strain_collec DROP FOREIGN KEY FK_3E5F3C8069B9E007');
        $this->addSql('ALTER TABLE strain_collec ADD CONSTRAINT FK_3E5F3C8069B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
