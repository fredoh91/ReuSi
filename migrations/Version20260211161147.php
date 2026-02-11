<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211161147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fichiers_signaux (id INT AUTO_INCREMENT NOT NULL, signal_lie_id INT DEFAULT NULL, nom_fichier VARCHAR(512) DEFAULT NULL, nom_original VARCHAR(255) DEFAULT NULL, taille INT DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_729413A46C5DCF6A (signal_lie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fichiers_signaux ADD CONSTRAINT FK_729413A46C5DCF6A FOREIGN KEY (signal_lie_id) REFERENCES `signal` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichiers_signaux DROP FOREIGN KEY FK_729413A46C5DCF6A');
        $this->addSql('DROP TABLE fichiers_signaux');
    }
}
