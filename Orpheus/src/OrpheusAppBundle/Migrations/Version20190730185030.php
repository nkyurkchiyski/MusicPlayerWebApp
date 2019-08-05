<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190730185030 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE playlists_songs (playlist_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_D7BF02DC6BBD148 (playlist_id), INDEX IDX_D7BF02DCA0BDB2F3 (song_id), PRIMARY KEY(playlist_id, song_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playlists_songs ADD CONSTRAINT FK_D7BF02DC6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlists (id)');
        $this->addSql('ALTER TABLE playlists_songs ADD CONSTRAINT FK_D7BF02DCA0BDB2F3 FOREIGN KEY (song_id) REFERENCES songs (id)');
        $this->addSql('DROP TABLE songs_playlists');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE songs_playlists (song_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_25B43772A0BDB2F3 (song_id), INDEX IDX_25B437726BBD148 (playlist_id), PRIMARY KEY(song_id, playlist_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE songs_playlists ADD CONSTRAINT FK_25B437726BBD148 FOREIGN KEY (playlist_id) REFERENCES playlists (id)');
        $this->addSql('ALTER TABLE songs_playlists ADD CONSTRAINT FK_25B43772A0BDB2F3 FOREIGN KEY (song_id) REFERENCES songs (id)');
        $this->addSql('DROP TABLE playlists_songs');
    }
}
