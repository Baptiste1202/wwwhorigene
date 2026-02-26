<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225123232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add concentration_unit + is_favorite and fix unsigned compatibility';
    }

    public function up(Schema $schema): void
    {
        // 1️⃣ Nouvelle colonne
        $this->addSql('ALTER TABLE drug_resistance_on_strain ADD concentration_unit VARCHAR(50) DEFAULT NULL');

        // 2️⃣ Aligner method_sequencing_type.id en INT UNSIGNED
        $this->addSql('ALTER TABLE method_sequencing_type CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');

        // 3️⃣ Aligner sequencing.id et sequencing.name_id en INT UNSIGNED
        $this->addSql('ALTER TABLE sequencing 
            CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL,
            CHANGE size_file size_file INT DEFAULT NULL,
            CHANGE name_id name_id INT UNSIGNED DEFAULT NULL
        ');

        // 4️⃣ FK sample → user
        $this->addSql('CREATE INDEX IDX_F10B76C3A76ED395 ON sample (user_id)');
        $this->addSql('ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        // 5️⃣ Favoris
        $this->addSql('ALTER TABLE strain ADD is_favorite TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE drug_resistance_on_strain DROP concentration_unit');

        $this->addSql('ALTER TABLE method_sequencing_type CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');

        $this->addSql('ALTER TABLE sample DROP FOREIGN KEY FK_F10B76C3A76ED395');
        $this->addSql('DROP INDEX IDX_F10B76C3A76ED395 ON sample');

        $this->addSql('ALTER TABLE sequencing 
            CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL,
            CHANGE size_file size_file BIGINT DEFAULT NULL,
            CHANGE name_id name_id INT UNSIGNED DEFAULT NULL
        ');

        $this->addSql('ALTER TABLE strain DROP is_favorite');
    }
}