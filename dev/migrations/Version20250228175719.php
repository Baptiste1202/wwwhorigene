<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228175719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain_method_sequencing DROP FOREIGN KEY FK_3C72C8A85D0538B2');
        $this->addSql('ALTER TABLE strain_method_sequencing DROP FOREIGN KEY FK_3C72C8A869B9E007');
        $this->addSql('DROP TABLE strain_method_sequencing');
        $this->addSql('ALTER TABLE method_sequencing ADD strain_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE method_sequencing ADD CONSTRAINT FK_DCBFD3B969B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('CREATE INDEX IDX_DCBFD3B969B9E007 ON method_sequencing (strain_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE strain_method_sequencing (strain_id INT NOT NULL, method_sequencing_id INT NOT NULL, INDEX IDX_3C72C8A869B9E007 (strain_id), INDEX IDX_3C72C8A85D0538B2 (method_sequencing_id), PRIMARY KEY(strain_id, method_sequencing_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE strain_method_sequencing ADD CONSTRAINT FK_3C72C8A85D0538B2 FOREIGN KEY (method_sequencing_id) REFERENCES method_sequencing (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain_method_sequencing ADD CONSTRAINT FK_3C72C8A869B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE method_sequencing DROP FOREIGN KEY FK_DCBFD3B969B9E007');
        $this->addSql('DROP INDEX IDX_DCBFD3B969B9E007 ON method_sequencing');
        $this->addSql('ALTER TABLE method_sequencing DROP strain_id');
    }
}
