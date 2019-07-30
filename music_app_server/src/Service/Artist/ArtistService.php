<?php


namespace App\Service\Artist;


use App\Entity\Artist;
use App\Repository\ArtistRepository;

class ArtistService implements ArtistServiceInterface
{

    /**
     * @var ArtistRepository
     */
    private $artistRepository;

    public function __construct(
        ArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    public function getOneByName(string $artistName)
    {
        return $this->artistRepository->findOneByName($artistName);
    }

    public function getAll()
    {
        return $this->artistRepository->findAll();
    }

    public function getOneById(int $id)
    {
        return $this->artistRepository->find($id);
    }

    public function create(Artist $artist): bool
    {
        return $this->artistRepository->save($artist);
    }

    public function edit(Artist $artist): bool
    {
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
    public function getOrCreateByName(string $artistName)
    {
        if (!isset($artistName) || ctype_space($artistName)){
            throw new \Exception("invalid data: name");
        }

        $artist = $this->getOneByName($artistName);

        if (null === $artist){
            $artist = new Artist();
            $artist->setName($artistName);
            $this->create($artist);
        }
        return $artist;
    }
}