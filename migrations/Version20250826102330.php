<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826102330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reunion_signal_signal (reunion_signal_id INT NOT NULL, signal_id INT NOT NULL, INDEX IDX_92000F47E7385087 (reunion_signal_id), INDEX IDX_92000F47D0CE460B (signal_id), PRIMARY KEY(reunion_signal_id, signal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reunion_signal_signal ADD CONSTRAINT FK_92000F47E7385087 FOREIGN KEY (reunion_signal_id) REFERENCES reunion_signal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reunion_signal_signal ADD CONSTRAINT FK_92000F47D0CE460B FOREIGN KEY (signal_id) REFERENCES `signal` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reunion_signal ADD reunion_annulee TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reunion_signal_signal DROP FOREIGN KEY FK_92000F47E7385087');
        $this->addSql('ALTER TABLE reunion_signal_signal DROP FOREIGN KEY FK_92000F47D0CE460B');
        $this->addSql('DROP TABLE reunion_signal_signal');
        $this->addSql('ALTER TABLE reunion_signal DROP reunion_annulee');
    }
}
