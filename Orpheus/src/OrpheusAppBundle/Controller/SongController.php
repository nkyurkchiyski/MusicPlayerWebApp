<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Entity\Song;
use OrpheusAppBundle\Form\SongType;
use OrpheusAppBundle\Service\Artist\ArtistServiceInterface;
use OrpheusAppBundle\Service\Genre\GenreServiceInterface;
use OrpheusAppBundle\Service\Song\SongServiceInterface;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use OrpheusAppBundle\Utils\ViolationsExtractor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        SongServiceInterface $songService,
        GenreServiceInterface $genreService,
        ArtistServiceInterface $artistService,
        UserServiceInterface $userService,
        ValidatorInterface $validator)
    {
        $this->songService = $songService;
        $this->genreService = $genreService;
        $this->artistService = $artistService;
        $this->userService = $userService;
        $this->validator = $validator;
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

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($song);
        $errors = ViolationsExtractor::extract($violations);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->songService->create($song);
            return $this->redirectToRoute('songs_my');
        }
        return $this->render('songs/create.html.twig', [
            'form' => $form->createView(),
            'genres' => $genres,
            'artists' => $artists,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/songs/edit/{id}", name="songs_edit")
     * @param int $id
     * @param Request $request
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editAction(int $id, Request $request)
    {
        $song = $this->songService->getOneById($id);
        $currentUser = $this->userService->currentUser();

        if ($song === null ||
            (!$currentUser->isSongCreator($song) &&
                !$currentUser->isAdmin())) {
            return $this->redirectToRoute("orpheus_index");
        }
        $genres = $this->genreService->getAll();
        $artists = $this->artistService->getAll();

        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($song);
        $errors = ViolationsExtractor::extract($violations);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->songService->edit($song, false);
                return $this->redirectToRoute('songs_details', ['id' => $song->getId()]);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $this->render('songs/edit.html.twig', [
            'form' => $form->createView(),
            'genres' => $genres,
            'artists' => $artists,
            'song' => $song,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/songs/delete/{id}", name="songs_delete")
     * @param int $id
     * @param Request $request
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction(int $id, Request $request)
    {
        $song = $this->songService->getOneById($id);
        $currentUser = $this->userService->currentUser();

        if ($song === null ||
            (!$currentUser->isSongCreator($song) &&
                !$currentUser->isAdmin())) {
            return $this->redirectToRoute("orpheus_index");
        }

        $genres = $this->genreService->getAll();
        $artists = $this->artistService->getAll();

        $form = $this->createForm(SongType::class, $song);

        if ($request->isMethod('post')) {
            $this->songService->delete($song);
            return $this->redirectToRoute("orpheus_index");
        }
        return $this->render('songs/delete.html.twig', [
            'form' => $form->createView(),
            'genres' => $genres,
            'artists' => $artists,
            'song' => $song
        ]);
    }

    /**
     * @Route("/songs/all", name="songs_all")
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function allAction()
    {
        $songs = $this->songService->getAll();
        return $this->render('songs/all.html.twig', ['songs' => $songs]);
    }

    /**
     * @Route("/songs/my", name="songs_my")
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function myAction()
    {
        $songs = $this->userService->currentUser()->getSongs();
        return $this->render('songs/my.html.twig', ['songs' => $songs]);
    }

    /**
     * @Route("/songs/details/{id}", name="songs_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function detailsAction(int $id, Request $request)
    {
        $song = $this->songService->getOneById($id);
        $count = $song->getPlayedCount() + 1;

        $song->setPlayedCount($count);
        $playlists = $this->userService->currentUser()->getPlaylists();
        $errors = $request->query->get("errors");

        $this->songService->edit($song, true);
        return $this->render('songs/details.html.twig', [
            'song' => $song,
            'playlists' => $playlists,
            'errors' => $errors
        ]);
    }

}
