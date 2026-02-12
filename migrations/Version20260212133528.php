<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212133528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fichiers_reunions_signal (id INT AUTO_INCREMENT NOT NULL, reunion_signal_liee_id INT DEFAULT NULL, nom_fichier VARCHAR(512) DEFAULT NULL, nom_original VARCHAR(512) DEFAULT NULL, taille INT DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_38BAFE061F229A69 (reunion_signal_liee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fichiers_reunions_signal ADD CONSTRAINT FK_38BAFE061F229A69 FOREIGN KEY (reunion_signal_liee_id) REFERENCES reunion_signal (id)');
        $this->addSql('ALTER TABLE fichiers_signaux CHANGE nom_original nom_original VARCHAR(512) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichiers_reunions_signal DROP FOREIGN KEY FK_38BAFE061F229A69');
        $this->addSql('DROP TABLE fichiers_reunions_signal');
        $this->addSql('ALTER TABLE fichiers_signaux CHANGE nom_original nom_original VARCHAR(255) DEFAULT NULL');
    }
}
