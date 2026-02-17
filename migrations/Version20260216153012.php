<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216153012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE liens_reunions_signal (id INT AUTO_INCREMENT NOT NULL, liens_reunions_signal_id INT DEFAULT NULL, libelle VARCHAR(255) DEFAULT NULL, url VARCHAR(2048) DEFAULT NULL, INDEX IDX_75A72685AD3A941 (liens_reunions_signal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE liens_reunions_signal ADD CONSTRAINT FK_75A72685AD3A941 FOREIGN KEY (liens_reunions_signal_id) REFERENCES reunion_signal (id)');
        $this->addSql('ALTER TABLE reunion_signal ADD commentaire LONGTEXT DEFAULT NULL, DROP reunion_annulee');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liens_reunions_signal DROP FOREIGN KEY FK_75A72685AD3A941');
        $this->addSql('DROP TABLE liens_reunions_signal');
        $this->addSql('ALTER TABLE reunion_signal ADD reunion_annulee TINYINT(1) DEFAULT NULL, DROP commentaire');
    }
}
