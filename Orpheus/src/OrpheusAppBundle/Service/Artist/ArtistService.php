<?php

namespace OrpheusAppBundle\Service\Artist;


use OrpheusAppBundle\Entity\Artist;
use OrpheusAppBundle\Entity\Song;
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

    /**
     * @param Artist $artist
     * @return bool
     * @throws \Exception
     */
    public function create(Artist $artist): bool
    {
        $this->checkCredentials();
        $this->checkNameCreate($artist->getName());

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
        $this->checkNameEdit($artist->getId(),$artist->getName());
        $this->checkUnknownName($artist->getId());
        return $this->artistRepository->update($artist);
    }

    /**
     * @param Artist $artist
     * @return bool
     * @throws \Exception
     */
    public function delete(Artist $artist): bool
    {
        $this->checkCredentials();
        $this->checkUnknownName($artist->getId());

        $userSongs = $artist->getSongs();
        $unknownArtist = $this->getOneByName("Unknown");

        /** @var Song $song */
        foreach ($userSongs as $song){
            $song->setArtist($unknownArtist);
        }

        return $this->artistRepository->remove($artist);
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

    /**
     * @param int $id
     * @param string $name
     * @throws \Exception
     */
    private function checkNameEdit(int $id, string $name): void
    {
        $artistPresent = $this->getOneByName($name);
        if ($artistPresent !== null && $artistPresent->getId() !== $id) {
            throw new \Exception(ErrorMessage::INVALID_ARTIST_NAME);
        }
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    private function checkNameCreate(string $name): void
    {
        $artistPresent = $this->getOneByName($name);
        if ($artistPresent !== null) {
            throw new \Exception(ErrorMessage::INVALID_ARTIST_NAME);
        }
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function checkUnknownName(int $id): void
    {
        $artist = $this->getOneByName("Unknown");
        if ($artist->getId() === $id) {
            throw new \Exception("You cannot edit/delete this artist");
        }
    }
}