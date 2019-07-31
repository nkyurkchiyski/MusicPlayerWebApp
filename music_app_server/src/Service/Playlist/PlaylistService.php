<?php


namespace App\Service\Playlist;


use App\Entity\Playlist;
use App\Entity\Song;
use App\Repository\PlaylistRepository;
use App\Service\User\UserServiceInterface;

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

    public function edit(Playlist $playlist): bool
    {
        $playlist->setUser($this->userService->currentUser());
        return $this->playlistRepository->update($playlist);
    }

    public function delete(Playlist $playlist): bool
    {
        return $this->playlistRepository->remove($playlist);
    }

    public function addSongToPlaylist(Song $song, Playlist $playlist): bool
    {
        $playlist=$playlist->addSong($song);
        return $this->playlistRepository->save($playlist);
    }

    public function removeSongFromPlaylist(Song $song, Playlist $playlist): bool
    {
        $playlist->removeSong($song);
        return $this->playlistRepository->save($playlist);
    }
}