<?php


namespace OrpheusAppBundle\Service\Song;


use OrpheusAppBundle\Entity\Song;

interface SongServiceInterface
{
    public function create(Song $song): bool;
    public function edit(Song $song, bool $isPlay): bool;
    public function delete(Song $song): bool;
    public function getOneById(int $id):?Song;
    public function getAll();
}