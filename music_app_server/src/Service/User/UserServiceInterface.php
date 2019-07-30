<?php


namespace App\Service\User;


use App\Entity\User;

interface UserServiceInterface
{
    public function findOneByEmail(string $email) : ?User;
    public function create(User $user) : bool ;
    public function findOneById (int $id) : ?User;
    public function findOne (User $user) : ?User;
    public function currentUser () :  ?User;
}