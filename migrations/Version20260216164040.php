<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216164040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liens_reunions_signal DROP FOREIGN KEY FK_75A72685AD3A941');
        $this->addSql('DROP INDEX IDX_75A72685AD3A941 ON liens_reunions_signal');
        $this->addSql('ALTER TABLE liens_reunions_signal ADD reunion_signal_id INT NOT NULL, DROP liens_reunions_signal_id');
        $this->addSql('ALTER TABLE liens_reunions_signal ADD CONSTRAINT FK_75A72685E7385087 FOREIGN KEY (reunion_signal_id) REFERENCES reunion_signal (id)');
        $this->addSql('CREATE INDEX IDX_75A72685E7385087 ON liens_reunions_signal (reunion_signal_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liens_reunions_signal DROP FOREIGN KEY FK_75A72685E7385087');
        $this->addSql('DROP INDEX IDX_75A72685E7385087 ON liens_reunions_signal');
        $this->addSql('ALTER TABLE liens_reunions_signal ADD liens_reunions_signal_id INT DEFAULT NULL, DROP reunion_signal_id');
        $this->addSql('ALTER TABLE liens_reunions_signal ADD CONSTRAINT FK_75A72685AD3A941 FOREIGN KEY (liens_reunions_signal_id) REFERENCES reunion_signal (id)');
        $this->addSql('CREATE INDEX IDX_75A72685AD3A941 ON liens_reunions_signal (liens_reunions_signal_id)');
    }
}
