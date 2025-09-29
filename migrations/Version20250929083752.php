<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929083752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE suivi (id INT AUTO_INCREMENT NOT NULL, signal_lie_id INT DEFAULT NULL, reunion_signal_id INT DEFAULT NULL, rdd_lie_id INT DEFAULT NULL, numero_suivi INT NOT NULL, description_suivi LONGTEXT DEFAULT NULL, pilote_ds VARCHAR(255) DEFAULT NULL, user_create VARCHAR(255) DEFAULT NULL, user_modif VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2EBCCA8F6C5DCF6A (signal_lie_id), INDEX IDX_2EBCCA8FE7385087 (reunion_signal_id), UNIQUE INDEX UNIQ_2EBCCA8FEF691B6A (rdd_lie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F6C5DCF6A FOREIGN KEY (signal_lie_id) REFERENCES `signal` (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8FE7385087 FOREIGN KEY (reunion_signal_id) REFERENCES reunion_signal (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8FEF691B6A FOREIGN KEY (rdd_lie_id) REFERENCES releve_de_decision (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F6C5DCF6A');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8FE7385087');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8FEF691B6A');
        $this->addSql('DROP TABLE suivi');
    }
}
