<?php


namespace App\Service\Playlist;


use App\Entity\Playlist;
use App\Entity\Song;
use App\Repository\PlaylistRepository;
use App\Service\User\UserServiceInterface;
use App\Utils\ErrorMessage;

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
        return $this->playlistRepository->findAll();
    }

    public function getOneById(int $id): ?Playlist
    {
        return $this->playlistRepository->find($id);
    }

    public function getAllByUserId(int $userId)
    {
        return $this->playlistRepository->findBy(['user'=>$userId]);
    }

    public function create(Playlist $playlist): bool
    {
        $playlist->setUser($this->userService->currentUser());
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
        $playlist->setUser($this->userService->currentUser());
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
}