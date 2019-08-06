<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Entity\Song;
use OrpheusAppBundle\Form\SongType;
use OrpheusAppBundle\Service\Artist\ArtistServiceInterface;
use OrpheusAppBundle\Service\Genre\GenreServiceInterface;
use OrpheusAppBundle\Service\Song\SongServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends Controller
{
    /**
     * @var SongServiceInterface
     */
    private $songService;
    /**
     * @var GenreServiceInterface
     */
    private $genreService;
    /**
     * @var ArtistServiceInterface
     */
    private $artistService;

    public function __construct(
        SongServiceInterface $songService,
        GenreServiceInterface $genreService,
        ArtistServiceInterface $artistService)
    {
        $this->songService = $songService;
        $this->genreService = $genreService;
        $this->artistService = $artistService;
    }

    /**
     * @Route("/songs/create", name="songs_create")
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function createAction(Request $request)
    {
        $song = new Song();
        $genres = $this->genreService->getAll();
        $artists = $this->artistService->getAll();

        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->songService->create($song);
            return $this->redirectToRoute('orpheus_index');
        }
        return $this->render('songs/create.html.twig',
            [
                'form' => $form->createView(),
                'genres' => $genres,
                'artists' => $artists,
            ]);
    }

    /**
     * @Route("/songs/edit/{id}", name="songs_edit")
     * @param int $id
     * @param Request $request
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editAction(int $id,Request $request)
    {
        $song = $this->songService->getOneById($id);

        if ($song === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        $genres = $this->genreService->getAll();
        $artists = $this->artistService->getAll();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $this->songService->edit($song,false);

            return $this->redirectToRoute('songs_details',array(
                'id' => $song->getId()
            ));
        }
        return $this->render('songs/edit.html.twig',
            [
                'form' => $form->createView(),
                'genres' => $genres,
                'artists' => $artists,
                'song' =>$song
            ]);
    }

    /**
     * @Route("/songs/delete/{id}", name="songs_delete")
     * @param int $id
     * @param Request $request
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction(int $id,Request $request)
    {
        $song = $this->songService->getOneById($id);

        if ($song === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        $genres = $this->genreService->getAll();
        $artists = $this->artistService->getAll();

        $form = $this->createForm(SongType::class, $song);

        if ($request->isMethod('post')) {
            $this->songService->delete($song);
            return $this->redirectToRoute("orpheus_index");
        }
        return $this->render('songs/delete.html.twig',
            [
                'form' => $form->createView(),
                'genres' => $genres,
                'artists' => $artists,
                'song' =>$song
            ]);
    }

    /**
     * @Route("/songs/all", name="songs_all")
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function allAction(Request $request)
    {
        return null;
    }

    /**
     * @Route("/songs/my", name="songs_my")
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function myAction(Request $request)
    {
        return null;
    }

    /**
     * @Route("/songs/details/{id}", name="songs_details")
     * @param int $id
     * @return Response
     */
    public function detailsAction(int $id)
    {
        $song = $this->songService->getOneById($id);
        $count = $song->getPlayedCount()+1;
        $song->setPlayedCount($count);

        $this->songService->edit($song,true);
        return $this->render('songs/details.html.twig', ['song' => $song]);
    }
}
