<?php


namespace OrpheusAppBundle\Service\Role;


use OrpheusAppBundle\Repository\RoleRepository;

class RoleService implements RoleServiceInterface
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getOneByName(string $name)
    {
        return $this->roleRepository->findOneBy(
            ['name' => $name]
        );
    }

    public function getAll()
    {
        return $this->roleRepository->findAll();
    }
}