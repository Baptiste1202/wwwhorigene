<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221122142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE strain_collec (strain_id INT NOT NULL, collec_id INT NOT NULL, INDEX IDX_3E5F3C8069B9E007 (strain_id), INDEX IDX_3E5F3C80584D4E9A (collec_id), PRIMARY KEY(strain_id, collec_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE strain_collec ADD CONSTRAINT FK_3E5F3C8069B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('ALTER TABLE strain_collec ADD CONSTRAINT FK_3E5F3C80584D4E9A FOREIGN KEY (collec_id) REFERENCES collec (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72514956FD');
        $this->addSql('DROP INDEX IDX_A630CD72514956FD ON strain');
        $this->addSql('ALTER TABLE strain DROP collection_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain_collec DROP FOREIGN KEY FK_3E5F3C8069B9E007');
        $this->addSql('ALTER TABLE strain_collec DROP FOREIGN KEY FK_3E5F3C80584D4E9A');
        $this->addSql('DROP TABLE strain_collec');
        $this->addSql('ALTER TABLE strain ADD collection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72514956FD FOREIGN KEY (collection_id) REFERENCES collec (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A630CD72514956FD ON strain (collection_id)');
    }
}
