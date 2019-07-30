<?php


namespace App\Service\Role;


interface RoleServiceInterface
{
    public function findOneByName(string $name);
}