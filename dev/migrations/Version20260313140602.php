<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260313140602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move measurement type from DrugResistance to DrugResistanceOnStrain (PostgreSQL compatible)';
    }

    public function up(Schema $schema): void
    {
        // 1. Suppression de l'ancienne colonne
        $this->addSql('ALTER TABLE drug_resistance DROP COLUMN type');

        // 2. Ajout de la nouvelle colonne
        // Note : Si la table est déjà peuplée, on ajoute une valeur par défaut 
        // ou on la permet NULL temporairement pour éviter une erreur PGSQL.
        $this->addSql('ALTER TABLE drug_resistance_on_strain ADD measurement_type VARCHAR(50) DEFAULT \'unknown\' NOT NULL');
        
        // Optionnel : Supprimer la valeur par défaut après l'ajout si vous voulez forcer l'applicatif
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN measurement_type DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE drug_resistance ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE drug_resistance_on_strain DROP COLUMN measurement_type');
    }
}
