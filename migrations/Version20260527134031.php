<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260527134031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `signal` ADD import_excel TINYINT(1) DEFAULT NULL, ADD ne_pas_afficher_ecran_reunion TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi ADD import_excel TINYINT(1) DEFAULT NULL, ADD ne_pas_afficher_ecran_reunion TINYINT(1) DEFAULT NULL, ADD ref_fic_excel VARCHAR(255) DEFAULT NULL, ADD id_liaisons_signaux_fic_excel INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `signal` DROP import_excel, DROP ne_pas_afficher_ecran_reunion');
        $this->addSql('ALTER TABLE suivi DROP import_excel, DROP ne_pas_afficher_ecran_reunion, DROP ref_fic_excel, DROP id_liaisons_signaux_fic_excel');
    }
}
