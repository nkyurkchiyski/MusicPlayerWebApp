<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Role")
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
     * @ORM\OneToMany(targetEntity="Playlist", mappedBy="user")
     */
    private $playlists;

    /**
     * @ORM\OneToMany(targetEntity="Song", mappedBy="user")
     */
    private $songs;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->songs = new ArrayCollection();
        $this->isActive = true;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles()
    {
        $userRoles = [];
        foreach ($this->roles as $role){
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
        return (string) $this->password;
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
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlaylists()
    {
        return $this->playlists;
    }

    public function getIsActive():bool
    {
        return $this->isActive;
    }

    public function setIsActive($isActive): self
    {
        $this->isActive = $isActive;

        return $this;
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
        $this->roles[] = $role;
        return $this;
    }
}
