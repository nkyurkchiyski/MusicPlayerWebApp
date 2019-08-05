<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Entity\User;
use OrpheusAppBundle\Form\UserType;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/register", name="users_register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $this->userService->create($user);
            return $this->redirectToRoute("security_login");
        }
        return $this->render("users/register.html.twig",[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/profile",name = "users_profile")
     *
     */
    public function profile(){
        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);

        $currentUser = $userRepository->find($this->getUser());

        return $this->render("users/profile.html.twig",
            ['users'=>$currentUser]);

    }
}
