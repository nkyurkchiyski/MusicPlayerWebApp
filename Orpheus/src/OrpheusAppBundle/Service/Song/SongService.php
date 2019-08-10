<?php


namespace OrpheusAppBundle\Service\Song;

use OrpheusAppBundle\Entity\Song;
use OrpheusAppBundle\Repository\GenreRepository;
use OrpheusAppBundle\Repository\SongRepository;
use OrpheusAppBundle\Service\Artist\ArtistServiceInterface;
use OrpheusAppBundle\Service\User\UserServiceInterface;
use OrpheusAppBundle\Utils\ErrorMessage;

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

    public function getOneById(int $id): ?Song
    {
        return $this->songRepository->find($id);
    }

    public function getAll()
    {
        return $this->songRepository->findBy([], ['dateAdded' => 'DESC']);
    }

    public function create(Song $song): bool
    {
        $song = $this->mapSong($song);
        $song->setUser($this->userService->currentUser());
        return $this->songRepository->save($song);
    }

    /**
     * @param Song $song
     * @param bool $isPlay
     * @return bool
     * @throws \Exception
     */
    public function edit(Song $song, bool $isPlay): bool
    {
        $this->checkCredentials($song, $isPlay);
        $song = $this->mapSong($song);

        return $this->songRepository->update($song);
    }

    /**
     * @param Song $song
     * @return bool
     * @throws \Exception
     */
    public function delete(Song $song): bool
    {
        $this->checkCredentials($song, false);
        return $this->songRepository->remove($song);
    }

    public function mapSong(Song $song): Song
    {
        $artist = $this->artistService->getOrCreateByName($song->getArtist()->getName());
        $genre = $this->genreRepository->findOneByName($song->getGenre()->getName());
        $songUrl = $song->getSongUrl();

        if (strpos($songUrl, 'spotify') !== false &&
            strpos($songUrl, 'embed') == false) {
            $songUrl = $this->createSongEmbedLink($song->getSongUrl());
            $song->setSongUrl($songUrl);
        }

        $song->setArtist($artist);
        $song->setGenre($genre);
        return $song;
    }

    /**
     * @param Song $song
     * @param bool $isPlay
     * @throws \Exception
     */
    private function checkCredentials(Song $song, bool $isPlay): void
    {
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isAdmin() &&
            !$currentUser->isSongCreator($song) &&
            !$isPlay) {
            throw new \Exception(ErrorMessage::INVALID_CREDENTIALS);
        }
    }

    private function createSongEmbedLink(string $link): string
    {
        $newStr = substr_replace($link, "embed/", strlen("https://open.spotify.com/"), 0);
        return $newStr;
    }

}

