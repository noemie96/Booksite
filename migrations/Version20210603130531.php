<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210603130531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE books_genres (books_id INT NOT NULL, genres_id INT NOT NULL, INDEX IDX_6C215D1A7DD8AC20 (books_id), INDEX IDX_6C215D1A6A3B2603 (genres_id), PRIMARY KEY(books_id, genres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE books_genres ADD CONSTRAINT FK_6C215D1A7DD8AC20 FOREIGN KEY (books_id) REFERENCES books (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_genres ADD CONSTRAINT FK_6C215D1A6A3B2603 FOREIGN KEY (genres_id) REFERENCES genres (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE genres_books');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE genres_books (genres_id INT NOT NULL, books_id INT NOT NULL, INDEX IDX_238DA5C66A3B2603 (genres_id), INDEX IDX_238DA5C67DD8AC20 (books_id), PRIMARY KEY(genres_id, books_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE genres_books ADD CONSTRAINT FK_238DA5C66A3B2603 FOREIGN KEY (genres_id) REFERENCES genres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genres_books ADD CONSTRAINT FK_238DA5C67DD8AC20 FOREIGN KEY (books_id) REFERENCES books (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE books_genres');
    }
}
