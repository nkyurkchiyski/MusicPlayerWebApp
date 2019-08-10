<?php

namespace OrpheusAppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="artists")
 * @ORM\Entity(repositoryClass="OrpheusAppBundle\Repository\ArtistRepository")
 */
class Artist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     *
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Regex(
     *     pattern="/^(http|https).+/",
     *     message="The image url has to start with http or https"
     * )
     *
     */
    private $imageUrl;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Song", mappedBy="artist",orphanRemoval=false)
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getSongs(): Collection
    {
        return $this->songs;
    }
}
