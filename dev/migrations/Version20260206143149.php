<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206143149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrates method_sequencing to sequencing with MethodSequencingType, removes depot table (PostgreSQL Compatible)';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Create new tables
        // PostgreSQL utilise SERIAL pour l'auto-incrément
        $this->addSql('CREATE TABLE IF NOT EXISTS method_sequencing_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        
        // Création de la table sequencing
        $this->addSql('CREATE TABLE IF NOT EXISTS sequencing (id SERIAL NOT NULL, date TIMESTAMP DEFAULT NULL, name_file VARCHAR(255) DEFAULT NULL, size_file BIGINT DEFAULT NULL, type_file VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, name_id INT DEFAULT NULL, strain_id INT DEFAULT NULL, PRIMARY KEY (id))');
        
        // Création des index
        $this->addSql('CREATE INDEX IDX_EACB1D171179CD6 ON sequencing (name_id)');
        $this->addSql('CREATE INDEX IDX_EACB1D169B9E007 ON sequencing (strain_id)');
        
        // Step 2: Migrate data from method_sequencing to method_sequencing_type
        $this->addSql('
            INSERT INTO method_sequencing_type (name)
            SELECT DISTINCT name 
            FROM method_sequencing 
            WHERE name IS NOT NULL 
            AND name NOT IN (SELECT name FROM method_sequencing_type)
        ');
        
        // Step 3: Migrate data from method_sequencing to sequencing
        $this->addSql('
            INSERT INTO sequencing (id, date, name_file, size_file, type_file, description, comment, name_id, strain_id)
            SELECT 
                ms.id,
                ms.date,
                ms.name_file,
                ms.size_file,
                ms.type_file,
                ms.description,
                ms.comment,
                (SELECT mst.id FROM method_sequencing_type mst WHERE mst.name = ms.name LIMIT 1) as name_id,
                ms.strain_id
            FROM method_sequencing ms
            WHERE ms.id NOT IN (SELECT id FROM sequencing)
        ');
        
        // Mise à jour de la séquence PostgreSQL pour éviter les erreurs de clés dupliquées futures
        $this->addSql('SELECT setval(pg_get_serial_sequence(\'sequencing\', \'id\'), (SELECT MAX(id) FROM sequencing))');

        // Step 4: Add foreign keys
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D171179CD6 FOREIGN KEY (name_id) REFERENCES method_sequencing_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sequencing ADD CONSTRAINT FK_EACB1D169B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        
        // Step 5: Drop old method_sequencing table
        $this->addSql('DROP TABLE IF EXISTS method_sequencing');
        
        // Step 6: Drop depot table and related foreign key from strain
        $this->addSql('ALTER TABLE strain DROP CONSTRAINT IF EXISTS FK_A630CD728510D4DE');
        $this->addSql('DROP INDEX IF EXISTS IDX_A630CD728510D4DE');
        $this->addSql('ALTER TABLE strain DROP COLUMN IF EXISTS depot_id');
        $this->addSql('DROP TABLE IF EXISTS depot');
    }

    public function down(Schema $schema): void
    {
        // Recreate depot table ("user" est entre quotes car mot réservé)
        $this->addSql('CREATE TABLE depot (id SERIAL NOT NULL, date_depot DATE NOT NULL, type VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, "user" VARCHAR(255) NOT NULL, sample VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        
        // Recreate method_sequencing table
        $this->addSql('CREATE TABLE method_sequencing (id SERIAL NOT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, name_file VARCHAR(255) DEFAULT NULL, strain_id INT DEFAULT NULL, type_file VARCHAR(255) DEFAULT NULL, date TIMESTAMP DEFAULT NULL, size_file BIGINT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_DCBFD3B969B9E007 ON method_sequencing (strain_id)');
        
        // Restore data
        $this->addSql('
            INSERT INTO method_sequencing (id, date, name_file, size_file, type_file, description, comment, name, strain_id)
            SELECT 
                s.id,
                s.date,
                s.name_file,
                s.size_file,
                s.type_file,
                s.description,
                s.comment,
                mst.name,
                s.strain_id
            FROM sequencing s
            LEFT JOIN method_sequencing_type mst ON s.name_id = mst.id
        ');

        // Reset sequence for method_sequencing rollback
        $this->addSql('SELECT setval(pg_get_serial_sequence(\'method_sequencing\', \'id\'), (SELECT MAX(id) FROM method_sequencing))');
        
        $this->addSql('ALTER TABLE method_sequencing ADD CONSTRAINT FK_DCBFD3B969B9E007 FOREIGN KEY (strain_id) REFERENCES strain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        
        // Drop new tables
        $this->addSql('ALTER TABLE sequencing DROP CONSTRAINT FK_EACB1D171179CD6');
        $this->addSql('ALTER TABLE sequencing DROP CONSTRAINT FK_EACB1D169B9E007');
        $this->addSql('DROP TABLE sequencing');
        $this->addSql('DROP TABLE method_sequencing_type');
        
        // Restore depot_id column in strain
        $this->addSql('ALTER TABLE strain ADD depot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE strain ADD CONSTRAINT FK_A630CD728510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A630CD728510D4DE ON strain (depot_id)');
    }
}
