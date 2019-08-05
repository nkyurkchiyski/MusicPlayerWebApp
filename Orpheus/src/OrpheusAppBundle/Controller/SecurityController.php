<?php

namespace OrpheusAppBundle\Controller;

use OrpheusAppBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends Controller
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
     * @Route ("/login", name="security_login")
     *
     * @return Response
     */
    public function login()
    {
        if ($this->userService->currentUser()){
            return $this->redirectToRoute("orpheus_index");
        }

        return $this->render('users/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception("Logout failed!");
    }

}
