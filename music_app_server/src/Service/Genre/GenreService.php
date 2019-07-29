<?php

namespace App\Service\Genre;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\Collection;

class GenreService implements GenreServiceInterface
{
    /**
     * @var GenreRepository
     */
    private $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    public function getAll()
    {
        return $this->genreRepository->findAll();
    }

    public function getOne(int $id): ?Genre
    {
        return $this->genreRepository->findOneBy(["id" => $id]);
    }
}