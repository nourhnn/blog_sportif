<?php
namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\Article\ArticleService;
use App\Service\User\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleService $articleService): Response
    {
        $articles = $articleService->getAllArticles();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
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
            // on met a jour l'objet $article avec les données du formulaire
            $article = $form->getData();
            // Mettre l'ID de l'utilisateur dans le champ ref de la table article
            $article->setRef($userId);
            // Persistez l'article dans la base de données
            $doctrine->getManager()->persist($article);
            $doctrine->getManager()->flush();
        
            $this->addFlash('success', 'article envoyé avec succés.');
        }
        
        return $this->render('article/create.html.twig', [
            'print_article' => $form,
        ]);
    }

    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function deleteArticle(int $id, ArticleService $articleService): RedirectResponse
    {
        if ($articleService->deleteOneArticle($id)) {
            $this->addFlash('success', 'L\'article a été supprimé.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'article.');
        }
        return $this->redirectToRoute('app_article');
    }
    #[Route('/article/update/{id}', name: 'app_article_update')]

    public function updatearticle(int $id, ArticleService $articleService, Request $request, ManagerRegistry $doctrine): Response
    {
        $article = $articleService->getOnearticle($id);
    
        $form = $this->createForm(ArticleType::class, $article, ['method' => 'POST']);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
    
            $this->addFlash('success', 'La tâche a été modifiée avec succès.');
        }
    
        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }    
}
