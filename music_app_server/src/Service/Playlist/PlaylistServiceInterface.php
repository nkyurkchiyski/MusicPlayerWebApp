<?php


namespace App\Service\Playlist;


use App\Entity\Playlist;
use App\Entity\Song;

interface PlaylistServiceInterface
{
    public function getOneById(int $id);
    public function getAll();
    public function getAllByUserId(int $userId);
    public function create(Playlist $playlist): bool;
    public function edit(Playlist $playlist): bool;
    public function delete(Playlist $playlist): bool;
    public function addSongToPlaylist(Song $song,Playlist $playlist):bool;
    public function removeSongFromPlaylist(Song $song,Playlist $playlist):bool;

}