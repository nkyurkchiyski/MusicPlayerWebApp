<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\User\UserServiceInterface;
use App\Utils\ErrorMessage;
use App\Utils\HttpError;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractFOSRestController
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
     * @Route("/api/register", name="api_register",methods={"POST"})
     * @param Request $request
     * @return View
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        try {
            $user = $this->processForm(
                $user,
                $request->request->all(),
                'POST');

            $this->userService->create($user);
            return $this->view(null, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            return $this->view(
                new HttpError($statusCode, $e->getMessage()),
                $statusCode);
        }
    }

    /**
     * @param $user
     * @param $params
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    private function processForm($user, $params, $method = 'PUT')
    {
        $form = $this->createForm(
            UserType::class,
            $user,
            ['method' => $method]);

        $clearMissing = $method != 'PATCH';

        $form->submit($params, $clearMissing);
        if ($form->isSubmitted()) {
            return $user;
        }
        throw new Exception(ErrorMessage::INVALID_DATA);
    }
}
