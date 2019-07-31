<?php


namespace App\Service\User;

use App\Entity\Playlist;
use App\Entity\User;
use App\Repository\PlaylistRepository;
use App\Repository\UserRepository;
use App\Service\Encryption\EncryptionServiceInterface;
use App\Service\Role\RoleServiceInterface;
use Symfony\Component\Security\Core\Security;

class UserService implements UserServiceInterface
{
    private $security;
    private $userRepository;
    private $encryptionService;
    private $roleService;
    private $playlistRepository;

    public function __construct(
        Security $security,
        UserRepository $userRepository,
        EncryptionServiceInterface $encryptionService,
        RoleServiceInterface $roleService,
        PlaylistRepository $playlistRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->encryptionService = $encryptionService;
        $this->roleService = $roleService;
        $this->playlistRepository = $playlistRepository;
    }

    public function getOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function create(User $user): bool
    {
        $this->mapUser($user);

        $userRole = $this->roleService->getOneByName(\GlobalConstant::ROLE_USER);

        $user->addRole($userRole);
        $this->userRepository->save($user);

        $playlist = new Playlist();
        $playlist->setName(\GlobalConstant::LIKED_PLAYLIST_NAME);
        $playlist->setUser($user);

        return $this->playlistRepository->save($playlist);

    }

    public function getOneById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getOne(User $user): ?User
    {
        return $this->userRepository->find($user);
    }

    /**
     * @return User|null|object
     */
    public function currentUser(): ?User
    {
        return $this->security->getUser();
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function edit(User $user): bool
    {
        $passwordHash =
            $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        return $this->userRepository->save($user);
    }

    /**
     * @param User $user
     * @return User|null
     * @throws \Exception
     */
    private function mapUser(User $user): ?User
    {
        if ($this->getOneByEmail($user->getEmail()) !== null) {
            throw new \Exception(\ErrorMessage::EMAIL_TAKEN);
        }
        $passwordHash =
            $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);

        return $user;
    }
}