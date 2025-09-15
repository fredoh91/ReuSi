<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912155251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mesures_rdd (id INT AUTO_INCREMENT NOT NULL, rdd_lie_id INT DEFAULT NULL, signal_lie_id INT DEFAULT NULL, lib_mesure VARCHAR(255) DEFAULT NULL, detail_commentaire LONGTEXT DEFAULT NULL, date_cloture_prev DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', date_cloture_effective DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', desactivate_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F87EA8DFEF691B6A (rdd_lie_id), INDEX IDX_F87EA8DF6C5DCF6A (signal_lie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesures_rdd ADD CONSTRAINT FK_F87EA8DFEF691B6A FOREIGN KEY (rdd_lie_id) REFERENCES releve_de_decision (id)');
        $this->addSql('ALTER TABLE mesures_rdd ADD CONSTRAINT FK_F87EA8DF6C5DCF6A FOREIGN KEY (signal_lie_id) REFERENCES `signal` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesures_rdd DROP FOREIGN KEY FK_F87EA8DFEF691B6A');
        $this->addSql('ALTER TABLE mesures_rdd DROP FOREIGN KEY FK_F87EA8DF6C5DCF6A');
        $this->addSql('DROP TABLE mesures_rdd');
    }
}
