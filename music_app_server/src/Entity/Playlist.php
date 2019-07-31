<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * * @ORM\Table(name="playlists")
 * @ORM\Entity(repositoryClass="App\Repository\PlaylistRepository")
 */
class Playlist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="playlists", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * @ORM\ManyToMany(targetEntity="Song", inversedBy="playlists",orphanRemoval=false,cascade={"persist"})
     * @ORM\JoinTable(name="playlists_songs",
     *      joinColumns={@ORM\JoinColumn(name="playlist_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="song_id", referencedColumnName="id")})
     */
    private $songs;


    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSongs(): ArrayCollection
    {
        return $this->songs;
    }

    public function addSong(Song $song):self
    {
        if (!$this->songs->contains($song)){
            $this->songs[] = $song;
        }

        return $this;

    }

    public function removeSong(Song $song):self
    {
        if ($this->songs->contains($song)){
            $this->songs->removeElement($song);
        }

        return $this;
    }

    public function getUser():?User
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}
