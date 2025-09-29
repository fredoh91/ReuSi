<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929143436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE direction_pole_concerne (id INT AUTO_INCREMENT NOT NULL, direction VARCHAR(255) DEFAULT NULL, pole_court VARCHAR(255) DEFAULT NULL, pole_long VARCHAR(255) DEFAULT NULL, pole_tres_court VARCHAR(255) DEFAULT NULL, inactif TINYINT(1) DEFAULT NULL, ordre_tri SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE direction_pole_concerne_signal (direction_pole_concerne_id INT NOT NULL, signal_id INT NOT NULL, INDEX IDX_96E6F4495FA16E1A (direction_pole_concerne_id), INDEX IDX_96E6F449D0CE460B (signal_id), PRIMARY KEY(direction_pole_concerne_id, signal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE direction_pole_concerne_signal ADD CONSTRAINT FK_96E6F4495FA16E1A FOREIGN KEY (direction_pole_concerne_id) REFERENCES direction_pole_concerne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE direction_pole_concerne_signal ADD CONSTRAINT FK_96E6F449D0CE460B FOREIGN KEY (signal_id) REFERENCES `signal` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE suivi ADD emetteur_suivi VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE direction_pole_concerne_signal DROP FOREIGN KEY FK_96E6F4495FA16E1A');
        $this->addSql('ALTER TABLE direction_pole_concerne_signal DROP FOREIGN KEY FK_96E6F449D0CE460B');
        $this->addSql('DROP TABLE direction_pole_concerne');
        $this->addSql('DROP TABLE direction_pole_concerne_signal');
        $this->addSql('ALTER TABLE suivi DROP emetteur_suivi');
    }
}
