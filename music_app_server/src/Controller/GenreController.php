<?php

namespace App\Controller;

use App\Service\Genre\GenreServiceInterface;
use App\Utils\ErrorMessage;
use App\Utils\HttpError;
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
        $data = $this->genreService->getOneById($id);

        if ($data === null){
            $statusCode = Response::HTTP_NOT_FOUND;

            return $this->view(
                new HttpError($statusCode,ErrorMessage::RESOURCE_NOT_FOUND),
                $statusCode);
        }

        return $this->view($data, Response::HTTP_OK);
    }
}
