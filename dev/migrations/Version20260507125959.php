<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507125959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename plasmyd tables and columns to plasmid without dropping data.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE plasmyd TO plasmid');
        $this->addSql('ALTER TABLE plasmid CHANGE name_plasmyd name_plasmid VARCHAR(255) NOT NULL');

        $this->addSql('RENAME TABLE strain_plasmyd TO strain_plasmid');
        $this->addSql('ALTER TABLE strain_plasmid CHANGE plasmyd_id plasmid_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE strain_plasmid CHANGE plasmid_id plasmyd_id INT NOT NULL');
        $this->addSql('RENAME TABLE strain_plasmid TO strain_plasmyd');

        $this->addSql('ALTER TABLE plasmid CHANGE name_plasmid name_plasmyd VARCHAR(255) NOT NULL');
        $this->addSql('RENAME TABLE plasmid TO plasmyd');
    }
}