<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260313140602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move measurement type from DrugResistance to DrugResistanceOnStrain';
    }

    public function up(Schema $schema): void
    {
        // remove old column from drug
        $this->addSql('ALTER TABLE drug_resistance DROP type');

        // add measurement type to experiment result
        $this->addSql('ALTER TABLE drug_resistance_on_strain ADD measurement_type VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // restore previous structure if rollback
        $this->addSql('ALTER TABLE drug_resistance ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE drug_resistance_on_strain DROP measurement_type');
    }
}