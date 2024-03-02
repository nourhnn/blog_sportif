<?php

namespace App\Service\User;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;

class UserService {

    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function getAllUserElement($email) {
        return $this->doctrine->getManager()->getRepository(Article::class)->findOneBy(['email'=>$email]); 
    }
}