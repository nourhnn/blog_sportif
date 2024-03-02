<?php

namespace App\Controller;

use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/create', name: 'app_article_create')]
    public function articleCreate(AuthenticationUtils $authenticationUtils, UserService $userId): Response
    {
        $email = $authenticationUtils->getLastUsername();
        $userId->getAllUserElement($email);
        dd($userId);

        return $this->render('article/create.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
}
