<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721202617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, ADD user_name VARCHAR(255) DEFAULT NULL, ADD date_creation DATETIME DEFAULT NULL, ADD date_desactivation DATETIME DEFAULT NULL, ADD date_derniere_connexion DATETIME DEFAULT NULL, ADD password_en_clair VARCHAR(255) DEFAULT NULL, ADD dmm VARCHAR(255) DEFAULT NULL, ADD pole_long VARCHAR(255) DEFAULT NULL, ADD pole_court VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP nom, DROP prenom, DROP user_name, DROP date_creation, DROP date_desactivation, DROP date_derniere_connexion, DROP password_en_clair, DROP dmm, DROP pole_long, DROP pole_court');
    }
}
