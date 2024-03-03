<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\User\UserService;
use Doctrine\Persistence\ManagerRegistry;
// use Symfony\Bridge\Doctrine\ManagerRegistry;
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
    public function articleCreate(Request $request, AuthenticationUtils $authenticationUtils, UserService $userService, ManagerRegistry $doctrine): Response
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

        if ($form->isSubmitted() && $form->isValid()) {
            // on met a jour l'objet $task avec les données du formulaire
            $article = $form->getData();
            // dd($article);
            //mettre l'id de l'user dans le champs ref de la table article
            $doctrine->getManager()->persist($article);
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'article envoyé avec succés.');
        }
        // dd($userId);
        return $this->render('article/create.html.twig', [
            'print_article' => $form,
        ]);
    }
}