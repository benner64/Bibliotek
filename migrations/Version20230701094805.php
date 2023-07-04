<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701094805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE series (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE series_author (series_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_DEDC3BF55278319C (series_id), INDEX IDX_DEDC3BF5F675F31B (author_id), PRIMARY KEY(series_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE series_author ADD CONSTRAINT FK_DEDC3BF55278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE series_author ADD CONSTRAINT FK_DEDC3BF5F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book ADD series_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3315278319C FOREIGN KEY (series_id) REFERENCES series (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A3315278319C ON book (series_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3315278319C');
        $this->addSql('ALTER TABLE series_author DROP FOREIGN KEY FK_DEDC3BF55278319C');
        $this->addSql('ALTER TABLE series_author DROP FOREIGN KEY FK_DEDC3BF5F675F31B');
        $this->addSql('DROP TABLE series');
        $this->addSql('DROP TABLE series_author');
        $this->addSql('DROP INDEX IDX_CBE5A3315278319C ON book');
        $this->addSql('ALTER TABLE book DROP series_id');
    }
}
