<?php

namespace App\Controller;

use App\Service\Genre\GenreServiceInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends AbstractFOSRestController
{

    /**
     * @var GenreServiceInterface
     */
    private $genreService;

    public function __construct(GenreServiceInterface $genreService)
    {
        $this->genreService = $genreService;
    }

    public function getGenresAction()
    {
        $data = $this->genreService->getAll();

        return $this->view($data, Response::HTTP_OK);
    }

    public function getGenreAction(int $id)
    {
        $data = $this->genreService->getOne($id);

        if ($data === null){
            return $this->view(['error' => 'resource not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->view($data, Response::HTTP_OK);
    }
}
