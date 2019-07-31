<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\Service\Song\SongServiceInterface;
use App\Utils\ErrorMessage;
use App\Utils\HttpError;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SongController extends AbstractFOSRestController
{
    /**
     * @var SongServiceInterface
     */
    private $songService;

    public function __construct(SongServiceInterface $songService)
    {
        $this->songService = $songService;
    }

    public function getSongsAction()
    {
        $data = $this->songService->getAll();

        return $this->view($data, Response::HTTP_OK);
    }

    public function getSongAction(int $id)
    {
        $data = $this->songService->getOneById($id);
        $statusCode = Response::HTTP_OK;
        if ($data === null) {
            $statusCode = Response::HTTP_NOT_FOUND;
            return $this->view(
                new HttpError($statusCode,ErrorMessage::RESOURCE_NOT_FOUND),
                $statusCode);
        }

        return $this->view($data, $statusCode);
    }

    public function postSongsAction(Request $request)
    {
        $song = new Song();
        try {
            $song = $this->processForm(
                $song,
                $request->request->all(),
                'POST');

            $this->songService->create($song);
            return $this->view(null, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            return $this->view(
                new HttpError($statusCode, $e->getMessage()),
                $statusCode);
        }

    }

    public function patchSongsAction(Request $request, int $id)
    {
        try {
            $song = $this->songService->getOneById($id);
            if (null === $song) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $song = $this->processForm(
                    $song,
                    $request->request->all(),
                    'PATCH');
                $this->songService->edit($song);
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

    public function deleteSongsAction(int $id)
    {
        try {
            $song = $this->songService->getOneById($id);
            if (null === $song) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $this->songService->delete($song);
                $statusCode = Response::HTTP_NO_CONTENT;
            }
            return  $this->view(null, $statusCode);
        } catch (\Exception $e) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            return $this->view(
                new HttpError($statusCode, $e->getMessage()),
                $statusCode);
        }
    }

    /**
     * @param $song
     * @param $params
     * @param string $method
     * @return mixed
     * @throws \Exception
     */
    private function processForm($song, $params, $method = 'PUT')
    {
        $form = $this->createForm(
            SongType::class,
            $song,
            ['method' => $method]);

        $clearMissing = $method != 'PATCH';

        $form->submit($params, $clearMissing);
        if ($form->isSubmitted()) {
            return $song;
        }
        throw new \Exception(ErrorMessage::INVALID_DATA);
    }


}
