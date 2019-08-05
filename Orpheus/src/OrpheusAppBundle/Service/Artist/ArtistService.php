<?php


namespace OrpheusAppBundle\Service\Artist;


use OrpheusAppBundle\Entity\Artist;
use OrpheusAppBundle\Repository\ArtistRepository;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use OrpheusAppBundle\Utils\ErrorMessage;

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
        return $this->artistRepository->findBy([], ['name' => 'ASC']);
    }

    public function getOneById(int $id): ?Artist
    {
        return $this->artistRepository->find($id);
    }

    public function create(Artist $artist): bool
    {
        return $this->artistRepository->save($artist);
    }

    /**
     * @param Artist $artist
     * @return bool
     * @throws \Exception
     */
    public function edit(Artist $artist): bool
    {
        $this->checkCredentials();
        return $this->artistRepository->update($artist);
    }

    public function delete(Artist $artist): bool
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
        if (!isset($artistName) || ctype_space($artistName)) {
            throw new \Exception(ErrorMessage::INVALID_ARTIST_NAME);
        }

        $artist = $this->getOneByName($artistName);

        if (null === $artist) {
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

    public function getAllSortedBySongsCount()
    {
        /** @var Artist[] $allArtists */
        $allArtists = $this->artistRepository->findBy([], ['name' => 'ASC']);
        usort($allArtists, function (Artist $a, Artist $b) {
            return count($b->getSongs()) - count($a->getSongs());
        });
        return $allArtists;
    }
}