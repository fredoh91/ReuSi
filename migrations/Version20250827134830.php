<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827134830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, signal_lie_id INT DEFAULT NULL, denomination VARCHAR(255) DEFAULT NULL, dci VARCHAR(255) DEFAULT NULL, dosage VARCHAR(255) DEFAULT NULL, voie VARCHAR(255) DEFAULT NULL, code_atc VARCHAR(255) DEFAULT NULL, lib_atc VARCHAR(255) DEFAULT NULL, type_procedure VARCHAR(255) DEFAULT NULL, code_cis VARCHAR(255) DEFAULT NULL, code_vu VARCHAR(255) DEFAULT NULL, code_dossier VARCHAR(255) DEFAULT NULL, nom_vu VARCHAR(255) DEFAULT NULL, codex TINYINT(1) DEFAULT NULL, laboratoire VARCHAR(255) DEFAULT NULL, id_laboratoire VARCHAR(255) DEFAULT NULL, adresse_contact VARCHAR(255) DEFAULT NULL, adresse_compl VARCHAR(255) DEFAULT NULL, code_post VARCHAR(255) DEFAULT NULL, nom_ville VARCHAR(255) DEFAULT NULL, tel_contact VARCHAR(255) DEFAULT NULL, fax_contact VARCHAR(255) DEFAULT NULL, dbo_pays_lib_abr VARCHAR(255) DEFAULT NULL, titulaire VARCHAR(255) DEFAULT NULL, id_titulaire VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, adresse_compl_expl VARCHAR(255) DEFAULT NULL, code_post_expl VARCHAR(255) DEFAULT NULL, nom_ville_expl VARCHAR(255) DEFAULT NULL, complement VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, medic_acces_libre TINYINT(1) DEFAULT NULL, prescription_delivrance LONGTEXT DEFAULT NULL, INDEX IDX_BE2DDF8C6C5DCF6A (signal_lie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C6C5DCF6A FOREIGN KEY (signal_lie_id) REFERENCES `signal` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C6C5DCF6A');
        $this->addSql('DROP TABLE produits');
    }
}
