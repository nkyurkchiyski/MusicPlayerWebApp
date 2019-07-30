<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Service\Playlist\PlaylistServiceInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractFOSRestController
{

    /**
     * @var PlaylistServiceInterface
     */
    private $playlistService;

    public function __construct(PlaylistServiceInterface $playlistService)
    {
        $this->playlistService = $playlistService;
    }

    public function getPlaylistAction(int $id)
    {
        $data = $this->playlistService->getOneById($id);

        return $this->view($data,Response::HTTP_OK);
        
    }

    public function getPlaylistsAction()
    {
        $data = $this->playlistService->getAll();

        return $this->view($data,Response::HTTP_OK);
    }

    public function postPlaylistAction(Request $request)
    {
        $playlist = new Playlist();
        try {
            $playlist = $this->processForm(
                $playlist,
                $request->request->all(),
                'POST');

            $this->playlistService->create($playlist);
            return $this->view(null, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function patchPlaylistAction(Request $request, int $id)
    {
        try {
            $playlist = $this->playlistService->getOneById($id);
            if (null === $playlist) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $playlist = $this->processForm(
                    $playlist,
                    $request->request->all(),
                    'PATCH');
                $this->playlistService->edit($playlist);
                $statusCode = Response::HTTP_NO_CONTENT;
            }
            return $this->view(null, $statusCode);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function deletePlaylistAction(int $id)
    {
        try {
            $playlist = $this->playlistService->getOneById($id);
            if (null === $playlist) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $this->playlistService->delete($playlist);
                $statusCode = Response::HTTP_NO_CONTENT;
            }
            return  $this->view(null, $statusCode);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $playlist
     * @param $params
     * @param string $method
     * @return mixed
     * @throws \Exception
     */
    private function processForm($playlist, $params, $method = 'PUT')
    {
        $form = $this->createForm(
            PlaylistType::class,
            $playlist,
            ['method' => $method]);

        $clearMissing = $method != 'PATCH';

        $form->submit($params, $clearMissing);
        if ($form->isSubmitted()) {
            return $playlist;
        }
        throw new \Exception('submitted data is invalid');
    }
}
