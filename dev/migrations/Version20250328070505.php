<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328070505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage ADD fridge VARCHAR(255) DEFAULT NULL, ADD shelf VARCHAR(255) DEFAULT NULL, ADD container_type VARCHAR(255) DEFAULT NULL, ADD container_position VARCHAR(255) DEFAULT NULL, DROP congelateur, DROP etagere, DROP type_conteneur, DROP position_conteneur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage ADD congelateur VARCHAR(255) DEFAULT NULL, ADD etagere VARCHAR(255) DEFAULT NULL, ADD type_conteneur VARCHAR(255) DEFAULT NULL, ADD position_conteneur VARCHAR(255) DEFAULT NULL, DROP fridge, DROP shelf, DROP container_type, DROP container_position');
    }
}
