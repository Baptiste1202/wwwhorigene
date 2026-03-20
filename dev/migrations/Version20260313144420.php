<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260313144420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN concentration DROP NOT NULL');
    }
    
    public function down(Schema $schema): void
    {
        // Attention : assurez-vous qu'il n'y a pas de NULL avant d'exécuter le down
        $this->addSql('ALTER TABLE drug_resistance_on_strain ALTER COLUMN concentration SET NOT NULL');
    }
}
