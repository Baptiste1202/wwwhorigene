<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328060948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain DROP room, DROP fridge, DROP shelf, DROP rack, DROP container_type, DROP container_position');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE strain ADD room VARCHAR(255) DEFAULT NULL, ADD fridge VARCHAR(255) DEFAULT NULL, ADD shelf VARCHAR(255) DEFAULT NULL, ADD rack VARCHAR(255) DEFAULT NULL, ADD container_type VARCHAR(255) DEFAULT NULL, ADD container_position VARCHAR(255) DEFAULT NULL');
    }
}
