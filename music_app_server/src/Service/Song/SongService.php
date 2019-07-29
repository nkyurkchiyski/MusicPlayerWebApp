<?php


namespace App\Service\Song;


use App\Entity\Artist;
use App\Entity\Song;
use App\Entity\Tag;
use App\Repository\GenreRepository;
use App\Repository\SongRepository;
use App\Repository\TagRepository;
use App\Service\Artist\ArtistServiceInterface;

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
     * @var TagRepository
     */
    private $tagRepository;
    /**
     * @var ArtistServiceInterface
     */
    private $artistService;

    public function __construct(
        SongRepository $songRepository,
        GenreRepository $genreRepository,
        TagRepository $tagRepository,
        ArtistServiceInterface $artistService)
    {
        $this->songRepository = $songRepository;
        $this->genreRepository = $genreRepository;
        $this->tagRepository = $tagRepository;
        $this->artistService = $artistService;
    }

    public function getOne(int $id): Song
    {
        return $this->songRepository->find($id);
    }

    public function getAll()
    {
        return $this->songRepository->findAll();
    }

    private function getOrCreateTags($params)
    {
        $tagNames = explode(', ',$params);
        $tags = [];
        foreach ($tagNames as $name){
            $tag = $this->tagRepository->findOneByName($name);
            if (null === $tag){
                $tag = new Tag();
                $tag->setName($name);
                $this->tagRepository->save($tag);
            }
            $tags[] = $tag;
        }
        return $tags;
    }

    /**
     * @param string $artistName
     * @return Artist
     * @throws \Exception
     */
    private function getOrCreateArtist(string $artistName)
    {
        if (null === $artistName){
            throw new \Exception("invalid data: name");
        }
        $artist = $this->artistService->getOneByName($artistName);
        if (null === $artist){
            $artist = new Artist();
            $artist->setName($artistName);
            $this->artistService->create($artist);
        }
        return $artist;
    }

    /**
     * @param Song $song
     * @param string $artistName
     * @param int $genreId
     * @param string $tagNames
     * @return bool
     * @throws \Exception
     */
    public function create(Song $song, string $artistName, int $genreId, string $tagNames): bool
    {
        $artist = $this->getOrCreateArtist($artistName);
        $genre = $this->genreRepository->find($genreId);
        $tags = $this->getOrCreateTags($tagNames);

        $song->setArtist($artist);
        $song->setGenre($genre);
        $song->setTags($tags);

        return $this->songRepository->save($song);

    }

    /**
     * @param Song $song
     * @param string $artistName
     * @param int $genreId
     * @param string $tagNames
     * @return bool
     * @throws \Exception
     */
    public function edit(Song $song, string $artistName, int $genreId, string $tagNames): bool
    {
        $artist = $this->getOrCreateArtist($artistName);
        $genre = $this->genreRepository->find($genreId);
        $tags = $this->getOrCreateTags($tagNames);

        $song->setArtist($artist);
        $song->setGenre($genre);
        $song->setTags($tags);

        return $this->songRepository->update($song);
    }

    public function delete(Song $song): bool
    {
        return $this->songRepository->remove($song);
    }
}