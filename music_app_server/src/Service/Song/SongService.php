<?php


namespace App\Service\Song;

use App\Entity\Song;
use App\Repository\GenreRepository;
use App\Repository\SongRepository;
use App\Service\Artist\ArtistServiceInterface;
use App\Service\User\UserServiceInterface;

class SongService implements SongServiceInterface
{
    /**
     * @var SongRepository
     */
    private $songRepository;
    /**
     * @var GenreRepository
     */
    private $genreRepository;
    /**
     * @var ArtistServiceInterface
     */
    private $artistService;
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        SongRepository $songRepository,
        GenreRepository $genreRepository,
        ArtistServiceInterface $artistService,
        UserServiceInterface $userService)
    {
        $this->songRepository = $songRepository;
        $this->genreRepository = $genreRepository;
        $this->artistService = $artistService;
        $this->userService = $userService;
    }

    public function getOneById(int $id): Song
    {
        return $this->songRepository->find($id);
    }

    public function getAll()
    {
        return $this->songRepository->findAll();
    }

    public function create(Song $song): bool
    {
        $song = $this->mapSong($song);
        return $this->songRepository->save($song);
    }

    public function edit(Song $song): bool
    {
        $song = $this->mapSong($song);
        return $this->songRepository->update($song);
    }

    public function delete(Song $song): bool
    {
        return $this->songRepository->remove($song);
    }

    public function mapSong(Song $song): Song
    {
        $artist = $this->artistService->getOrCreateByName($song->getArtist()->getName());
        $genre = $this->genreRepository->findOneByName($song->getGenre()->getName());

        $song->setArtist($artist);
        $song->setGenre($genre);
        $song->setUser($this->userService->currentUser());

        return $song;
    }

}

