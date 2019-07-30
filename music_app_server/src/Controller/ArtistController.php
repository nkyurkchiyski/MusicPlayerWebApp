<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Service\Artist\ArtistServiceInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArtistController extends AbstractFOSRestController
{

    /**
     * @var ArtistServiceInterface
     */
    private $artistService;

    public function __construct(ArtistServiceInterface $artistService)
    {
        $this->artistService = $artistService;
    }

    public function getArtistsAction()
    {
        $data = $this->artistService->getAll();

        return $this->view($data, Response::HTTP_OK);

    }

    public function getArtistAction(int $id)
    {
        $data = $this->artistService->getOneById($id);

        if ($data === null) {
            return $this->view(['error' => 'resource not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->view($data, Response::HTTP_OK);
    }

    public function patchArtistsAction(Request $request, int $id)
    {
        $artist = $this->artistService->getOneById($id);
        try {
            if ($artist){
                $artist = $this->processForm(
                    $artist,
                    $request->request->all(),
                    'PATCH');
                $this->artistService->edit($artist);
            }
            return $this->view(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function postArtistsAction(Request $request)
    {
        $artist = new Artist();
        try {
            $artist = $this->processForm(
                $artist,
                $request->request->all(),
                'POST');

            $this->artistService->create($artist);
            return $this->view(null, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $artist
     * @param $params
     * @param string $method
     * @return mixed
     * @throws \Exception
     */
    private function processForm($artist, $params, $method = 'PUT')
    {
        $form = $this->createForm(
            ArtistType::class,
            $artist,
            ['method' => $method]);

        $clearMissing = $method != 'PATCH';

        $form->submit($params,$clearMissing);
        if ($form->isSubmitted()) {
            return $artist;
        }
        throw new \Exception('submitted data is invalid');
    }
}
