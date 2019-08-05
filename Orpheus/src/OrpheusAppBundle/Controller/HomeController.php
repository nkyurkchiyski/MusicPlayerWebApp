<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Service\Song\SongServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @var SongServiceInterface
     */
    private $songService;

    public function __construct(SongServiceInterface $songService)
    {
        $this->songService = $songService;
    }

    /**
     * @Route("/", name="orpheus_index")
     */
    public function indexAction()
    {
        $songs = $this->songService->getAll();
        return $this->render('orpheus/index.html.twig', ['songs' => $songs]);
    }

}
