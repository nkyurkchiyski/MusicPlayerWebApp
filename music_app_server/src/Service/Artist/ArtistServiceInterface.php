<?php


namespace App\Service\Artist;


use App\Entity\Artist;

interface ArtistServiceInterface
{
    public function getOneByName(string $artistName): ?Artist;
    public function getOrCreateByName(string $artistName): ?Artist;
    public function getAll();
    public function getOneById(int $id): ?Artist;
    public function create(Artist $artist): bool;
    public function edit(Artist $artist): bool;
    public function delete(Artist $artist): bool;
}