<?php


namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Encryption\EncryptionServiceInterface;
use App\Service\Role\RoleServiceInterface;
use Symfony\Component\Security\Core\Security;

class UserService implements  UserServiceInterface
{
    private $security;
    private $userRepository;
    private $encryptionService;
    private $roleService;

    public function __construct(Security $security,
                                UserRepository $userRepository,
                                EncryptionServiceInterface $encryptionService,
                                RoleServiceInterface $roleService)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->encryptionService = $encryptionService;
        $this->roleService = $roleService;
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function create(User $user): bool
    {
        $passwordHash =
            $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        $userRole = $this->roleService->findOneByName('ROLE_USER');

        $user->addRole($userRole);
        return $this->userRepository->save($user);
    }

    public function findOneById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function findOne(User $user): ?User
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
}