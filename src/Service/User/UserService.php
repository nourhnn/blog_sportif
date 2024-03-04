<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserService {

    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function getAllUserElement($email) {

        return $this->doctrine->getManager()->getRepository(User::class)->findOneBy(['email'=>$email]); 
    }

    public function adminAccess(int $id) {
        $user = $this->doctrine->getManager()->getRepository(User::class)->findOneBy(['id'=>$id]); 
        // dd($user);
    }
}