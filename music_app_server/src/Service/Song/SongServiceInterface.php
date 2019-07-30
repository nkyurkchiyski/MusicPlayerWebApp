<?php


namespace App\Service\Song;


use App\Entity\Song;

interface SongServiceInterface
{
    public function create(Song $song): bool;
    public function edit(Song $song): bool;
    public function delete(Song $song): bool;
    public function getOneById(int $id):Song;
    public function getAll();
}