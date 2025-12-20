<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251220214156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statut_suivi (id INT AUTO_INCREMENT NOT NULL, suivi_lie_id INT DEFAULT NULL, lib_statut VARCHAR(255) DEFAULT NULL, date_mise_en_place DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_desactivation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', statut_actif TINYINT(1) DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1C41A5F16DEA1DFF (suivi_lie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statut_suivi ADD CONSTRAINT FK_1C41A5F16DEA1DFF FOREIGN KEY (suivi_lie_id) REFERENCES suivi (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_suivi DROP FOREIGN KEY FK_1C41A5F16DEA1DFF');
        $this->addSql('DROP TABLE statut_suivi');
    }
}
