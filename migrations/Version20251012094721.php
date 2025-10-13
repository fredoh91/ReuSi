<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251012094721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON direction_pole_concerne_signal');
        $this->addSql('ALTER TABLE direction_pole_concerne_signal ADD PRIMARY KEY (signal_id, direction_pole_concerne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON direction_pole_concerne_signal');
        $this->addSql('ALTER TABLE direction_pole_concerne_signal ADD PRIMARY KEY (direction_pole_concerne_id, signal_id)');
    }
}
