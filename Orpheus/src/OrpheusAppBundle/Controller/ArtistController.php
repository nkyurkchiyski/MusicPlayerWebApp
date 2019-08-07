<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Entity\Artist;
use OrpheusAppBundle\Form\ArtistType;
use OrpheusAppBundle\Service\Artist\ArtistServiceInterface;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController extends Controller
{
    /**
     * @var ArtistServiceInterface
     */
    private $artistService;
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        ArtistServiceInterface $artistService,
        UserServiceInterface $userService)
    {
        $this->artistService = $artistService;
        $this->userService = $userService;
    }

    /**
     * @Route("/artists/all", name="artists_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function allAction()
    {
        $artists = $this->artistService->getAllSortedBySongsCount();
        return $this->render('artists/all.html.twig', ['artists' => $artists]);
    }

    /**
     * @Route("/artists/details/{id}", name="artists_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function detailsAction(int $id)
    {
        $artist = $this->artistService->getOneById($id);
        return $this->render('artists/details.html.twig', ['artist' => $artist]);
    }

    /**
     * @Route("/artists/create", name="artists_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $artist = new Artist();

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->artistService->create($artist);
            return $this->redirectToRoute('orpheus_index');
        }
        return $this->render('artists/create.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/artists/edit/{id}", name="artists_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editAction(int $id, Request $request)
    {
        if (!$this->userService->currentUser()->isAdmin()){
            return $this->redirectToRoute("orpheus_index");
        }

        $artist = $this->artistService->getOneById($id);

        if ($artist === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $this->artistService->edit($artist);

            return $this->redirectToRoute('artists_details', array(
                'id' => $artist->getId()
            ));
        }
        return $this->render('artists/edit.html.twig',
            [
                'form' => $form->createView(),
                'artist' => $artist
            ]);
    }

    /**
     * @Route("/artists/delete/{id}", name="artists_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction(int $id, Request $request)
    {
        if (!$this->userService->currentUser()->isAdmin()) {
            return $this->redirectToRoute("orpheus_index");
        }

        $artist = $this->artistService->getOneById($id);

        if ($artist === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        if ($request->isMethod('post')) {
            $this->artistService->delete($artist);
            return $this->redirectToRoute("orpheus_index");
        }

        return $this->render('artists/delete.html.twig',
            [
                'artist' => $artist
            ]);
    }
}
