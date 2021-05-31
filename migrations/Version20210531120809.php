<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531120809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A92FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4A1B2A92FB88E14F ON books (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A92FB88E14F');
        $this->addSql('DROP INDEX IDX_4A1B2A92FB88E14F ON books');
    }
}
