<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908084617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statut_signal (id INT AUTO_INCREMENT NOT NULL, signal_lie_id INT DEFAULT NULL, lib_statut VARCHAR(255) NOT NULL, date_mise_en_place DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', date_desactivation DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', statut_actif TINYINT(1) DEFAULT NULL, INDEX IDX_C38334A16C5DCF6A (signal_lie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statut_signal ADD CONSTRAINT FK_C38334A16C5DCF6A FOREIGN KEY (signal_lie_id) REFERENCES `signal` (id)');
        $this->addSql('ALTER TABLE releve_de_decision ADD passage_ctp VARCHAR(255) DEFAULT NULL, ADD passage_rss VARCHAR(255) DEFAULT NULL, ADD emetteur_suivi VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `signal` ADD type_signal VARCHAR(255) NOT NULL, DROP statut_creation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_signal DROP FOREIGN KEY FK_C38334A16C5DCF6A');
        $this->addSql('DROP TABLE statut_signal');
        $this->addSql('ALTER TABLE releve_de_decision DROP passage_ctp, DROP passage_rss, DROP emetteur_suivi');
        $this->addSql('ALTER TABLE `signal` ADD statut_creation VARCHAR(255) DEFAULT NULL, DROP type_signal');
    }
}
