<?php


namespace App\Service\Artist;


use App\Entity\Artist;
use App\Repository\ArtistRepository;
use App\Service\User\UserServiceInterface;
use App\Utils\ErrorMessage;

class ArtistService implements ArtistServiceInterface
{

    /**
     * @var ArtistRepository
     */
    private $artistRepository;
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        ArtistRepository $artistRepository,
        UserServiceInterface $userService)
    {
        $this->artistRepository = $artistRepository;
        $this->userService = $userService;
    }

    public function getOneByName(string $artistName): ?Artist
    {
        return $this->artistRepository->findOneByName($artistName);
    }

    public function getAll()
    {
        return $this->artistRepository->findAll();
    }

    public function getOneById(int $id): ?Artist
    {
        return $this->artistRepository->find($id);
    }

    public function create(Artist $artist): bool
    {
        return $this->artistRepository->save($artist);
    }

    public function edit(Artist $artist): bool
    {
        $this->checkCredentials();
        return $this->artistRepository->update($artist);
    }

    public function delete(Artist $artist):bool
    {
        return $this->artistRepository->remove($artist);
    }

    /**
     * @param string $artistName
     * @return Artist
     * @throws \Exception
     */
    public function getOrCreateByName(string $artistName): ?Artist
    {
        if (!isset($artistName) || ctype_space($artistName)){
            throw new \Exception(ErrorMessage::INVALID_ARTIST_NAME);
        }

        $artist = $this->getOneByName($artistName);

        if (null === $artist){
            $artist = new Artist();
            $artist->setName($artistName);
            $this->create($artist);
        }
        return $artist;
    }

    /**
     * @throws \Exception
     */
    private function checkCredentials(): void
    {
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isAdmin()) {
            throw new \Exception(ErrorMessage::INVALID_CREDENTIALS);
        }
    }
}