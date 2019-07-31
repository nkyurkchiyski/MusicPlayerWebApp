<?php


namespace App\Service\User;


use App\Entity\User;

interface UserServiceInterface
{
    public function getOneByEmail(string $email) : ?User;
    public function create(User $user) : bool;
    public function edit(User $user) : bool;
    public function getOneById (int $id) : ?User;
    public function getOne (User $user) : ?User;
    public function currentUser () :  ?User;
}