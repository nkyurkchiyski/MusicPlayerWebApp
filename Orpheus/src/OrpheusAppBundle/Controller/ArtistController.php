<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Service\Artist\ArtistServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController extends Controller
{
    /**
     * @var ArtistServiceInterface
     */
    private $artistService;

    public function __construct(ArtistServiceInterface $artistService)
    {
        $this->artistService = $artistService;
    }

    /**
     * @Route("/artists", name="artists_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function allAction()
    {
        $artists = $this->artistService->getAllSortedBySongsCount();
        return $this->render('artists/all.html.twig', ['artists' => $artists]);
    }

    /**
     * @Route("/artists/{id}", name="artists_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function detailsAction(int $id)
    {
        $artist = $this->artistService->getOneById($id);
        return $this->render('artists/details.html.twig', ['artist' => $artist]);
    }
}
