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
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        ArtistServiceInterface $artistService,
        UserServiceInterface $userService,
        ValidatorInterface $validator)
    {
        $this->artistService = $artistService;
        $this->userService = $userService;
        $this->validator = $validator;
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
        if (!$this->userService->currentUser()->isAdmin()) {
            return $this->redirectToRoute("orpheus_index");
        }

        $artist = new Artist();
        $errors = [];

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($artist);
        $errors = array_merge($errors, $this->extractViolations($violations));

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->artistService->create($artist);
                return $this->redirectToRoute('artists_all');
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        return $this->render('artists/create.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $errors
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
        if (!$this->userService->currentUser()->isAdmin()) {
            return $this->redirectToRoute("orpheus_index");
        }

        $artist = $this->artistService->getOneById($id);
        $errors = [];

        if ($artist === null) {
            return $this->redirectToRoute("orpheus_index");
        }

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($artist);
        $errors = array_merge($errors, $this->extractViolations($violations));

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->artistService->edit($artist);

                return $this->redirectToRoute('artists_details', ['id' => $artist->getId()]);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $this->render('artists/edit.html.twig', [
            'form' => $form->createView(),
            'artist' => $artist,
            'errors' => $errors
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

    private function extractViolations(ConstraintViolationList $violationsList, $propertyPath = null)
    {
        $output = array();
        foreach ($violationsList as $violation) {
            $output[$violation->getPropertyPath()] = $violation->getMessage();
        }
        if (null !== $propertyPath) {
            if (array_key_exists($propertyPath, $output)) {
                $output = array($propertyPath => $output[$propertyPath]);
            } else {
                return array();
            }
        }
        return $output;
    }
}
