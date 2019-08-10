<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Entity\User;
use OrpheusAppBundle\Form\UserType;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        UserServiceInterface $userService,
        ValidatorInterface $validator)
    {
        $this->userService = $userService;
        $this->validator = $validator;
    }

    /**
     * @Route("/register", name="users_register")
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        if ($this->userService->currentUser()) {
            return $this->redirectToRoute("orpheus_index");
        }

        $user = new User();
        $errors = [];
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($user);
        $errors = array_merge($errors, $this->extractViolations($violations));

        if ($form->isSubmitted() && $form->isValid() && empty($errors)) {
            try {
                $this->userService->create($user);
                return $this->redirectToRoute("security_login");
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $this->render("users/register.html.twig", [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/profile",name = "users_profile")
     * @Route("/users/details/{id}", name="users_details")
     * @param int $id
     * @return Response
     */
    public function profileAction(?int $id)
    {
        $user = $this->userService->currentUser();

        if ($id !== null) {
            $user = $this->userService->getOneById($id);
        }

        return $this->render("users/profile.html.twig",
            ['user' => $user]);
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
