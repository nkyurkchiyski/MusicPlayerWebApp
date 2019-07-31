<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\User\UserServiceInterface;
use App\Utils\ErrorMessage;
use App\Utils\HttpError;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function getUserAction(int $id){
        $data = $this->userService->getOneById($id);
        $statusCode = Response::HTTP_OK;
        if ($data === null) {
            $statusCode = Response::HTTP_NOT_FOUND;
            return $this->view(
                new HttpError($statusCode,ErrorMessage::RESOURCE_NOT_FOUND),
                $statusCode);
        }

        return $this->view($data, $statusCode);
    }

    public function patchUsersAction(Request $request)
    {
        try {
            $user = $this->userService->currentUser();
            if (null === $user) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $user = $this->processForm(
                    $user,
                    $request->request->all(),
                    'PATCH');
                $this->userService->edit($user);
                $statusCode = Response::HTTP_NO_CONTENT;
            }
            return $this->view(null, $statusCode);
        } catch (\Exception $e) {
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
