<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250825133221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE releve_de_decision (id INT AUTO_INCREMENT NOT NULL, signal_lie_id INT DEFAULT NULL, reunion_signal_id INT DEFAULT NULL, numero_rdd INT DEFAULT NULL, description_rdd LONGTEXT DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_170EBFE86C5DCF6A (signal_lie_id), INDEX IDX_170EBFE8E7385087 (reunion_signal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reunion_signal (id INT AUTO_INCREMENT NOT NULL, date_reunion DATE DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `signal` (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description_signal LONGTEXT DEFAULT NULL, indication LONGTEXT DEFAULT NULL, date_creation DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', contexte LONGTEXT DEFAULT NULL, niveau_risque_initial VARCHAR(255) DEFAULT NULL, niveau_risque_final VARCHAR(255) DEFAULT NULL, ana_risque_comment LONGTEXT DEFAULT NULL, source_signal VARCHAR(255) DEFAULT NULL, ref_signal VARCHAR(255) DEFAULT NULL, identifiant_source VARCHAR(255) DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE releve_de_decision ADD CONSTRAINT FK_170EBFE86C5DCF6A FOREIGN KEY (signal_lie_id) REFERENCES `signal` (id)');
        $this->addSql('ALTER TABLE releve_de_decision ADD CONSTRAINT FK_170EBFE8E7385087 FOREIGN KEY (reunion_signal_id) REFERENCES reunion_signal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE releve_de_decision DROP FOREIGN KEY FK_170EBFE86C5DCF6A');
        $this->addSql('ALTER TABLE releve_de_decision DROP FOREIGN KEY FK_170EBFE8E7385087');
        $this->addSql('DROP TABLE releve_de_decision');
        $this->addSql('DROP TABLE reunion_signal');
        $this->addSql('DROP TABLE `signal`');
    }
}
