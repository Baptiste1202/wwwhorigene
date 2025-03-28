<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328062205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage ADD strain_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B3469B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id)');
        $this->addSql('CREATE INDEX IDX_547A1B3469B9E007 ON storage (strain_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B3469B9E007');
        $this->addSql('DROP INDEX IDX_547A1B3469B9E007 ON storage');
        $this->addSql('ALTER TABLE storage DROP strain_id');
    }
}
