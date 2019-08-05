<?php

namespace OrpheusAppBundle\Service\Genre;

use OrpheusAppBundle\Entity\Genre;

interface GenreServiceInterface
{
    public function getAll();
    public function getOneById(int $id):?Genre;
}