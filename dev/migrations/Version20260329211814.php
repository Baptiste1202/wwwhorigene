<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329211814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align signed integers for sequencing and method_sequencing_type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sequencing DROP FOREIGN KEY FK_EACB1D1450F8A42');
        $this->addSql('DROP INDEX IDX_EACB1D1450F8A42 ON sequencing');

        $this->addSql('ALTER TABLE method_sequencing_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE sequencing CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE method_sequencing_type_id method_sequencing_type_id INT DEFAULT NULL');

        $this->addSql('DROP INDEX id ON sequencing');

        $this->addSql('CREATE INDEX IDX_EACB1D1450F8A42 ON sequencing (method_sequencing_type_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D1450F8A42 FOREIGN KEY (method_sequencing_type_id) REFERENCES method_sequencing_type (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sequencing DROP FOREIGN KEY FK_EACB1D1450F8A42');
        $this->addSql('DROP INDEX IDX_EACB1D1450F8A42 ON sequencing');

        $this->addSql('ALTER TABLE sequencing CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE method_sequencing_type_id method_sequencing_type_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE method_sequencing_type CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');

        $this->addSql('CREATE INDEX IDX_EACB1D1450F8A42 ON sequencing (method_sequencing_type_id)');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D1450F8A42 FOREIGN KEY (method_sequencing_type_id) REFERENCES method_sequencing_type (id)');
    }
}