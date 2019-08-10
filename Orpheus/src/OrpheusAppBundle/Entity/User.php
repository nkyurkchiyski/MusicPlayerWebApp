<?php

namespace OrpheusAppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="OrpheusAppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Role",)
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Playlist", mappedBy="user",cascade={"persist"})
     */
    private $playlists;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Song", mappedBy="user")
     */
    private $songs;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->roles = [];
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this users.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function getRoles()
    {
        $userRoles = [];
        foreach ($this->roles as $role) {
            /** @var $role Role */
            $userRoles[] = $role->getRole();
        }
        return $userRoles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the users, clear it here
        // $this->plainPassword = null;
    }

    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * @param Song $song
     * @return bool
     */
    public function isSongCreator(Song $song)
    {
        return $song->getUser()->getId() == $this->getId();
    }

    /**
     * @param Playlist $playlist
     * @return bool
     */
    public function isPlaylistCreator(Playlist $playlist)
    {
        return $playlist->getUser()->getId() == $this->getId();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    public function addRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function addPlaylist(Playlist $playlist)
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist)
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
        }

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }
}
