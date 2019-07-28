<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190728111717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE albums (id INT AUTO_INCREMENT NOT NULL, artist_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, year INT NOT NULL, cover_art_url LONGTEXT DEFAULT NULL, INDEX IDX_F4E2474FB7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artists (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genres (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE songs (id INT AUTO_INCREMENT NOT NULL, genre_id INT DEFAULT NULL, album_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, played_count BIGINT NOT NULL, date_added DATE NOT NULL, INDEX IDX_BAECB19B4296D31F (genre_id), INDEX IDX_BAECB19B1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE songs_tags (song_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_8F53B435A0BDB2F3 (song_id), INDEX IDX_8F53B435BAD26311 (tag_id), PRIMARY KEY(song_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FB7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id)');
        $this->addSql('ALTER TABLE songs ADD CONSTRAINT FK_BAECB19B4296D31F FOREIGN KEY (genre_id) REFERENCES genres (id)');
        $this->addSql('ALTER TABLE songs ADD CONSTRAINT FK_BAECB19B1137ABCF FOREIGN KEY (album_id) REFERENCES albums (id)');
        $this->addSql('ALTER TABLE songs_tags ADD CONSTRAINT FK_8F53B435A0BDB2F3 FOREIGN KEY (song_id) REFERENCES songs (id)');
        $this->addSql('ALTER TABLE songs_tags ADD CONSTRAINT FK_8F53B435BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE songs DROP FOREIGN KEY FK_BAECB19B1137ABCF');
        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474FB7970CF8');
        $this->addSql('ALTER TABLE songs DROP FOREIGN KEY FK_BAECB19B4296D31F');
        $this->addSql('ALTER TABLE songs_tags DROP FOREIGN KEY FK_8F53B435A0BDB2F3');
        $this->addSql('ALTER TABLE songs_tags DROP FOREIGN KEY FK_8F53B435BAD26311');
        $this->addSql('DROP TABLE albums');
        $this->addSql('DROP TABLE artists');
        $this->addSql('DROP TABLE genres');
        $this->addSql('DROP TABLE songs');
        $this->addSql('DROP TABLE songs_tags');
        $this->addSql('DROP TABLE tags');
    }
}
