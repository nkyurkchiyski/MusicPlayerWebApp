<?php

namespace OrpheusAppBundle\Service\Playlist;

use Doctrine\Common\Collections\ArrayCollection;
use OrpheusAppBundle\Entity\Playlist;
use OrpheusAppBundle\Entity\Song;
use OrpheusAppBundle\Repository\PlaylistRepository;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use OrpheusAppBundle\Utils\ErrorMessage;

class PlaylistService implements PlaylistServiceInterface
{
    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        PlaylistRepository $playlistRepository,
        UserServiceInterface $userService)
    {
        $this->playlistRepository = $playlistRepository;
        $this->userService = $userService;
    }

    public function getAll()
    {
        return new ArrayCollection($this->playlistRepository->findAll());
    }

    public function getOneById(int $id): ?Playlist
    {
        return $this->playlistRepository->find($id);
    }

    public function getAllByUserId(int $userId)
    {
        return $this->playlistRepository->findBy(['users' => $userId]);
    }

    /**
     * @param Playlist $playlist
     * @return bool
     * @throws \Exception
     */
    public function create(Playlist $playlist): bool
    {
        $playlist->setUser($this->userService->currentUser());
        $this->checkNameCreate($playlist->getName());
        return $this->playlistRepository->save($playlist);
    }

    /**
     * @param Playlist $playlist
     * @return bool
     * @throws \Exception
     */
    public function edit(Playlist $playlist): bool
    {
        $this->checkCredentials($playlist);
        $this->checkLiked();
        $this->checkNameEdit($playlist->getId(), $playlist->getName());
        return $this->playlistRepository->update($playlist);
    }

    /**
     * @param Playlist $playlist
     * @return bool
     * @throws \Exception
     */
    public function delete(Playlist $playlist): bool
    {
        $this->checkCredentials($playlist);
        $this->checkLikedName($playlist->getName());
        return $this->playlistRepository->remove($playlist);
    }

    /**
     * @param Song $song
     * @param Playlist $playlist
     * @return bool
     * @throws \Exception
     */
    public function addSongToPlaylist(Song $song, Playlist $playlist): bool
    {
        $this->checkCredentials($playlist);
        $this->isSongPresent($song,$playlist);
        $playlist->addSong($song);
        return $this->playlistRepository->save($playlist);
    }

    /**
     * @param Song $song
     * @param Playlist $playlist
     * @return bool
     * @throws \Exception
     */
    public function removeSongFromPlaylist(Song $song, Playlist $playlist): bool
    {
        $this->checkCredentials($playlist);
        $playlist->removeSong($song);
        return $this->playlistRepository->save($playlist);
    }

    /**
     * @param int $id
     * @param string $name
     * @throws \Exception
     */
    private function checkNameEdit(int $id, string $name): void
    {
        /** @var Playlist $playlistPresent */
        $playlistPresent = $this->playlistRepository->findOneBy(['name' => $name]);
        if ($playlistPresent !== null && $playlistPresent->getId() !== $id) {
            throw new \Exception(ErrorMessage::PLAYLIST_ALREADY_EXISTS);
        }
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    private function checkNameCreate(string $name): void
    {
        $userPlaylists = $this->userService->currentUser()->getPlaylists();
        $isPresent = $userPlaylists->exists(function($key, $element) use ($name){
            return $element->getName() === $name;
        });

        if ($isPresent) {
            throw new \Exception(ErrorMessage::PLAYLIST_ALREADY_EXISTS);
        }
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    private function checkLikedName(string $name): void
    {
        if ($name === "Liked") {
            throw new \Exception(ErrorMessage::PLAYLIST_IMMUTABLE);
        }
    }

    /**
     * @param Playlist $playlist
     * @throws \Exception
     */
    private function checkCredentials(Playlist $playlist): void
    {
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isAdmin() &&
            !$currentUser->isPlaylistCreator($playlist)) {
            throw new \Exception(ErrorMessage::INVALID_CREDENTIALS);
        }
    }

    /**
     * @param Song $song
     * @param Playlist $playlist
     * @throws \Exception
     */
    private function isSongPresent(Song $song, Playlist $playlist){
        $isPresent = $playlist->getSongs()->exists(function($key, $element) use ($song){
            return $song->getId() === $element->getId();
        });

        if ($isPresent){
            throw new \Exception(ErrorMessage::SONG_ALREADY_PRESENT);
        }
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    private function checkLiked(){
        $playlists = $this->userService->currentUser()->getPlaylists();
        $isPresent = $playlists->exists(function($key, $element){
            return "Liked" === $element->getName();
        });

        if (!$isPresent){
            throw new \Exception(ErrorMessage::PLAYLIST_IMMUTABLE);
        }
    }
}