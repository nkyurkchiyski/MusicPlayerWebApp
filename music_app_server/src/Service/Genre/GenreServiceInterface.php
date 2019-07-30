<?php

namespace App\Service\Genre;

use App\Entity\Genre;
use Doctrine\Common\Collections\Collection;

interface GenreServiceInterface
{
    public function getAll();
    public function getOneById(int $id):?Genre;
}