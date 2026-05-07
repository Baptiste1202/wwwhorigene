<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507130546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename strain_plasmid indexes after plasmyd to plasmid refactor.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE strain_plasmid 
             RENAME INDEX idx_eb2d4a3669b9e007 
             TO IDX_A1EF586769B9E007'
        );

        $this->addSql(
            'ALTER TABLE strain_plasmid 
             RENAME INDEX idx_eb2d4a363b91781 
             TO IDX_A1EF586763598003'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE strain_plasmid 
             RENAME INDEX IDX_A1EF586769B9E007 
             TO idx_eb2d4a3669b9e007'
        );

        $this->addSql(
            'ALTER TABLE strain_plasmid 
             RENAME INDEX IDX_A1EF586763598003 
             TO idx_eb2d4a363b91781'
        );
    }
}