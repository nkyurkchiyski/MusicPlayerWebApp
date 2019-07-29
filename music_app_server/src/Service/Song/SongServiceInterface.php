<?php


namespace App\Service\Song;


use App\Entity\Song;

interface SongServiceInterface
{
    public function create(Song $song, string $artistName, int $genreId,string $tagNames): bool;
    public function edit(Song $song ,string $artistName, int $genreId, string $tagNames): bool;
    public function delete(Song $song): bool;
    public function getOne(int $id):Song;
    public function getAll();
}