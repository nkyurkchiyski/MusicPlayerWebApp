<?php

namespace OrpheusAppBundle\Service\Genre;

use OrpheusAppBundle\Entity\Genre;
use OrpheusAppBundle\Repository\GenreRepository;

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
        return $this->genreRepository->findBy([],['name'=>'ASC']);
    }

    public function getOneById(int $id): ?Genre
    {
        return $this->genreRepository->findOneBy(["id" => $id]);
    }
}