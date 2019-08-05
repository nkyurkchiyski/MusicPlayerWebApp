<?php


namespace OrpheusAppBundle\Service\Role;


interface RoleServiceInterface
{
    public function getOneByName(string $name);
    public function getAll();
}