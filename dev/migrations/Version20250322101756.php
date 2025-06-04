<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250322101756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain_transformability DROP FOREIGN KEY FK_424257B469B9E007');
        $this->addSql('ALTER TABLE strain_transformability DROP FOREIGN KEY FK_424257B4108272A0');
        $this->addSql('DROP TABLE strain_transformability');
        $this->addSql('ALTER TABLE transformability ADD strain_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transformability ADD CONSTRAINT FK_5AB058BE69B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('CREATE INDEX IDX_5AB058BE69B9E007 ON transformability (strain_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE strain_transformability (strain_id INT NOT NULL, transformability_id INT NOT NULL, INDEX IDX_424257B469B9E007 (strain_id), INDEX IDX_424257B4108272A0 (transformability_id), PRIMARY KEY(strain_id, transformability_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE strain_transformability ADD CONSTRAINT FK_424257B469B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE strain_transformability ADD CONSTRAINT FK_424257B4108272A0 FOREIGN KEY (transformability_id) REFERENCES transformability (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transformability DROP FOREIGN KEY FK_5AB058BE69B9E007');
        $this->addSql('DROP INDEX IDX_5AB058BE69B9E007 ON transformability');
        $this->addSql('ALTER TABLE transformability DROP strain_id');
    }
}
