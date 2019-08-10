<?php

namespace OrpheusAppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OrpheusAppBundle\Entity\Playlist;
use OrpheusAppBundle\Form\PlaylistType;
use OrpheusAppBundle\Service\Playlist\PlaylistServiceInterface;
use OrpheusAppBundle\Service\Song\SongServiceInterface;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends Controller
{
    /**
     * @var PlaylistServiceInterface
     */
    private $playlistService;
    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var SongServiceInterface
     */
    private $songService;

    public function __construct(
        PlaylistServiceInterface $playlistService,
        UserServiceInterface $userService,
        SongServiceInterface $songService)
    {
        $this->playlistService = $playlistService;
        $this->userService = $userService;
        $this->songService = $songService;
    }

    /**
     * @Route("/playlists/details/{id}", name="playlists_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function detailsAction(int $id)
    {
        $playlist = $this->playlistService->getOneById($id);

        if ($playlist == null) {
            return $this->redirectToRoute("orpheus_index");
        }

        return $this->render('playlists/details.html.twig', ['playlist' => $playlist]);
    }

    /**
     * @Route("/playlists/create", name="playlists_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $playlist = new Playlist();
        $errors = [];

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->playlistService->create($playlist);
                return $this->redirectToRoute('orpheus_index');
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        return $this->render('playlists/create.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $errors
            ]);
    }

    /**
     * @Route("/playlists/edit/{id}", name="playlists_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editAction(int $id, Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $playlist = $this->playlistService->getOneById($id);
        $errors = [];

        if ($playlist == null ||
            (!$currentUser->isPlaylistCreator($playlist) &&
                !$currentUser->isAdmin())) {
            return $this->redirectToRoute("orpheus_index");
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $this->playlistService->edit($playlist);

                return $this->redirectToRoute('playlists_details', ['id' => $playlist->getId()]);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        return $this->render('playlists/edit.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/playlists/delete/{id}", name="playlists_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction(int $id, Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $playlist = $this->playlistService->getOneById($id);
        $errors = [];

        if ($playlist == null ||
            (!$currentUser->isPlaylistCreator($playlist) &&
                !$currentUser->isAdmin())) {
            return $this->redirectToRoute("orpheus_index");
        }

        if ($request->isMethod('post')) {
            try {
                $this->playlistService->delete($playlist);
                return $this->redirectToRoute("orpheus_index");
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $this->render('playlists/delete.html.twig', [
            'playlist' => $playlist,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/playlists/my", name="playlists_my")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function myAction()
    {
        $currentUser = $this->userService->currentUser();
        $playlists = $currentUser->getPlaylists();

        return $this->render('playlists/my.html.twig',
            [
                'playlists' => $playlists
            ]);
    }

    /**
     * @Route("/playlists/all", name="playlists_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function allAction()
    {
        /** @var ArrayCollection $playlists */
        $playlists = $this->playlistService->getAll()
            ->filter(function ($entry) {
                return $entry->getName() !== "Liked";
            });

        return $this->render('playlists/all.html.twig', [
            'playlists' => $playlists
        ]);
    }

    /**
     * @Route("/playlists/add/songs/{id}", name="playlists_addSong")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function addSongToPlaylistAction(int $id, Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $song = $this->songService->getOneById($id);
        $playlistId = $request->request->get('playlistId');

        if ($song === null || $playlistId === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        $playlist = $this->playlistService->getOneById($playlistId);
        $errors = [];

        try {
            if ($currentUser->getId() !== $playlist->getUser()->getId()) {
                return $this->redirectToRoute("orpheus_index");
            }

            $this->playlistService->addSongToPlaylist($song, $playlist);

            return $this->redirectToRoute("playlists_details", ['id' => $playlist->getId()]);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return $this->redirectToRoute("songs_details", ['id' => $song->getId(), 'errors' => $errors]);
    }

    /**
     * @Route("/playlists/{playlistId}/remove/songs/{songId}", name="playlists_removeSong")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $playlistId
     * @param int $songId
     * @return Response
     */
    public function removeSongFromPlaylistAction(int $playlistId, int $songId)
    {
        $song = $this->songService->getOneById($songId);
        $playlist = $this->playlistService->getOneById($playlistId);

        if ($song === null || $playlist === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        $this->playlistService->removeSongFromPlaylist($song, $playlist);

        return $this->redirectToRoute("playlists_details", ['id' => $playlist->getId()]);
    }

}
