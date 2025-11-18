<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118195033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gamme (id INT AUTO_INCREMENT NOT NULL, lib_gamme VARCHAR(255) DEFAULT NULL, direction VARCHAR(255) DEFAULT NULL, pole_court VARCHAR(255) DEFAULT NULL, pole_long VARCHAR(255) DEFAULT NULL, pole_tres_court VARCHAR(255) DEFAULT NULL, inactif TINYINT(1) DEFAULT NULL, ordre_tri SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE signal_gamme (signal_id INT NOT NULL, gamme_id INT NOT NULL, INDEX IDX_C81A70AD0CE460B (signal_id), INDEX IDX_C81A70AD2FD85F1 (gamme_id), PRIMARY KEY(signal_id, gamme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE signal_gamme ADD CONSTRAINT FK_C81A70AD0CE460B FOREIGN KEY (signal_id) REFERENCES `signal` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE signal_gamme ADD CONSTRAINT FK_C81A70AD2FD85F1 FOREIGN KEY (gamme_id) REFERENCES gamme (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signal_gamme DROP FOREIGN KEY FK_C81A70AD0CE460B');
        $this->addSql('ALTER TABLE signal_gamme DROP FOREIGN KEY FK_C81A70AD2FD85F1');
        $this->addSql('DROP TABLE gamme');
        $this->addSql('DROP TABLE signal_gamme');
    }
}
