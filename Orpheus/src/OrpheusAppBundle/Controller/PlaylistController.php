<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Entity\Playlist;
use OrpheusAppBundle\Form\PlaylistType;
use OrpheusAppBundle\Service\Playlist\PlaylistServiceInterface;
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

    public function __construct(
        PlaylistServiceInterface $playlistService,
        UserServiceInterface $userService)
    {
        $this->playlistService = $playlistService;
        $this->userService = $userService;

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

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->playlistService->create($playlist);
            return $this->redirectToRoute('orpheus_index');
        }
        return $this->render('playlists/create.html.twig',
            [
                'form' => $form->createView()
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

        if ($playlist == null ||
            (!$currentUser->isPlaylistCreator($playlist) &&
                !$currentUser->isAdmin())) {
            return $this->redirectToRoute("orpheus_index");
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->playlistService->edit($playlist);

            return $this->redirectToRoute('playlists_details', array(
                'id' => $playlist->getId()
            ));
        }
        return $this->render('playlists/edit.html.twig',
            [
                'form' => $form->createView(),
                'playlist' => $playlist
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

        if ($playlist == null ||
            (!$currentUser->isPlaylistCreator($playlist) &&
                !$currentUser->isAdmin())) {
            return $this->redirectToRoute("orpheus_index");
        }

        if ($request->isMethod('post')) {
            $this->playlistService->delete($playlist);
            return $this->redirectToRoute("orpheus_index");
        }

        return $this->render('playlists/delete.html.twig',
            [
                'playlist' => $playlist
            ]);
    }

    /**
     * @Route("/playlists/my", name="playlists_my")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function myPlaylistsAction()
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
    public function allPlaylistsAction()
    {
        $playlists = $this->playlistService->getAll();

        return $this->render('playlists/all.html.twig',
            [
                'playlists' => $playlists
            ]);
    }

}
