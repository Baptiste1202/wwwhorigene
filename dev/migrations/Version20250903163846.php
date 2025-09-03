<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903163846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phenotype ADD phenotype_type_id INT DEFAULT NULL, DROP phenotype_type');
        $this->addSql('ALTER TABLE phenotype ADD CONSTRAINT FK_482C09B46ECD26A5 FOREIGN KEY (phenotype_type_id) REFERENCES phenotype_type (id)');
        $this->addSql('CREATE INDEX IDX_482C09B46ECD26A5 ON phenotype (phenotype_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phenotype DROP FOREIGN KEY FK_482C09B46ECD26A5');
        $this->addSql('DROP INDEX IDX_482C09B46ECD26A5 ON phenotype');
        $this->addSql('ALTER TABLE phenotype ADD phenotype_type VARCHAR(255) DEFAULT NULL, DROP phenotype_type_id');
    }
}
