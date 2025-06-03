<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227083833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain ADD date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD72B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A630CD72B03A8386 ON strain (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain DROP FOREIGN KEY FK_A630CD72B03A8386');
        $this->addSql('DROP INDEX IDX_A630CD72B03A8386 ON strain');
        $this->addSql('ALTER TABLE strain DROP date');
    }
}
