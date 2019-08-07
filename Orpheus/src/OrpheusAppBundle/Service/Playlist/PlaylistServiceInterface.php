<?php


namespace OrpheusAppBundle\Service\Playlist;


use OrpheusAppBundle\Entity\Playlist;
use OrpheusAppBundle\Entity\Song;

interface PlaylistServiceInterface
{
    public function getOneById(int $id): ?Playlist;
    public function getAll();
    public function getAllByUserId(int $userId);
    public function create(Playlist $playlist): bool;
    public function edit(Playlist $playlist): bool;
    public function delete(Playlist $playlist): bool;
    public function addSongToPlaylist(Song $song,Playlist $playlist):bool;
    public function removeSongFromPlaylist(Song $song,Playlist $playlist):bool;

}