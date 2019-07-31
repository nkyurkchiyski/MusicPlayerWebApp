<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Service\Playlist\PlaylistServiceInterface;
use App\Service\Song\SongServiceInterface;
use App\Utils\ErrorMessage;
use App\Utils\HttpError;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlaylistController extends AbstractFOSRestController
{

    /**
     * @var PlaylistServiceInterface
     */
    private $playlistService;
    /**
     * @var SongServiceInterface
     */
    private $songService;

    public function __construct(
        PlaylistServiceInterface $playlistService,
        SongServiceInterface $songService)
    {
        $this->playlistService = $playlistService;
        $this->songService = $songService;
    }

    public function getPlaylistAction(int $id)
    {
        $data = $this->playlistService->getOneById($id);
        $statusCode = Response::HTTP_OK;

        if ($data === null) {
            $statusCode = Response::HTTP_NOT_FOUND;

            return $this->view(
                new HttpError($statusCode,ErrorMessage::RESOURCE_NOT_FOUND),
                $statusCode);
        }

        return $this->view($data, $statusCode);
        
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
            $statusCode = Response::HTTP_BAD_REQUEST;
            return $this->view(
                new HttpError($statusCode, $e->getMessage()),
                $statusCode);
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
            $statusCode = Response::HTTP_BAD_REQUEST;
            return $this->view(
                new HttpError($statusCode, $e->getMessage()),
                $statusCode);
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
            $statusCode = Response::HTTP_BAD_REQUEST;
            return $this->view(
                new HttpError($statusCode, $e->getMessage()),
                $statusCode);
        }
    }

    public function postPlaylistsSongsAction(int $playlistId,int $songId)
    {
        try {
            $playlist = $this->playlistService->getOneById($playlistId);
            $song = $this->songService->getOneById($songId);

            if (null === $playlist ||
                null==$song) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $this->playlistService->addSongToPlaylist($song,$playlist);
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

    public function deletePlaylistsSongsAction(int $playlistId,int $songId)
    {
        try {
            $playlist = $this->playlistService->getOneById($playlistId);
            $song = $this->songService->getOneById($songId);

            if (null === $playlist ||
                null==$song) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $this->playlistService->removeSongFromPlaylist($song,$playlist);
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
        throw new \Exception(ErrorMessage::INVALID_DATA);
    }
}
