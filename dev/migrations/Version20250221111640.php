<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221111640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE strain_project (strain_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_E35934AE69B9E007 (strain_id), INDEX IDX_E35934AE166D1F9C (project_id), PRIMARY KEY(strain_id, project_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE strain_project ADD CONSTRAINT FK_E35934AE69B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_project ADD CONSTRAINT FK_E35934AE166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72166D1F9C');
        $this->addSql('DROP INDEX IDX_A630CD72166D1F9C ON strain');
        $this->addSql('ALTER TABLE strain DROP project_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain_project DROP FOREIGN KEY FK_E35934AE69B9E007');
        $this->addSql('ALTER TABLE strain_project DROP FOREIGN KEY FK_E35934AE166D1F9C');
        $this->addSql('DROP TABLE strain_project');
        $this->addSql('ALTER TABLE strain ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A630CD72166D1F9C ON strain (project_id)');
    }
}
