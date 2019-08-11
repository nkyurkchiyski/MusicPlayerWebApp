<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Service\Genre\GenreServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends Controller
{
    /**
     * @var GenreServiceInterface
     */
    private $genreService;

    public function __construct(GenreServiceInterface $genreService)
    {
        $this->genreService = $genreService;
    }

    /**
     * @Route("/genres/all", name="genres_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function allAction()
    {
        $genres = $this->genreService->getAllSortedBySongsCount();
        return $this->render('genres/all.html.twig', ['genres' => $genres]);
    }

    /**
     * @Route("/genres/{id}", name="genres_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function detailsAction(int $id)
    {
        $genre = $this->genreService->getOneById($id);

        if ($genre === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        return $this->render('genres/details.html.twig', ['genre' => $genre]);
    }

}
