<?php

namespace App\Service\Genre;

use App\Entity\Genre;
use Doctrine\Common\Collections\Collection;

interface GenreServiceInterface
{
    public function getAll();
    public function getOne(int $id):?Genre;
}