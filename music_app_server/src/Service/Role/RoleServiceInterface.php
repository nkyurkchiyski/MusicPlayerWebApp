<?php


namespace App\Service\Role;


interface RoleServiceInterface
{
    public function getOneByName(string $name);
    public function getAll();
}