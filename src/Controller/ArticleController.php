<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function articleCreate(Request $request, AuthenticationUtils $authenticationUtils, UserService $userService): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

    
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $userService->getAllUserElement($lastUsername);
        $user = $userService->getAllUserElement($lastUsername);
        $userId = null;
        if ($user !== null) {
            $userId = $user->getId();
        }
        // dd($userId);
        return $this->render('article/create.html.twig', [
            'print_article' => $form,
        ]);
    }
}