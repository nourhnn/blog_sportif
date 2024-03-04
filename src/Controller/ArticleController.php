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
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour publiez un article.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $lastUsername = $authenticationUtils->getLastUsername();
        
        $userService->getAllUserElement($lastUsername);
        $user = $userService->getAllUserElement($lastUsername);
        $userId = null;
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            if ($user !== null) {
                $userId = $user->getId();
            
                // dd($userId);
                // if($userId!== null){
                //     $article->setRef($userId);
                // }
                // elseif($userId === null){
                //     return $this->redirectToRoute('app_login');
                // }
                try{
                    $article->setRef($userId);
                }
                catch(\Exception $e){
                    return $this->redirectToRoute('app_login');
                }
                $article = $form->getData();
                $article->articleId($userId, $doctrine); 
            }
            // Récupérer la description de l'article du formulaire
        
            // Persistez l'article dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
        
            $this->addFlash('success', 'article envoyé avec succès.');
        }
        
        return $this->render('article/create.html.twig', [
            'print_article' => $form,
        ]);
    }
    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function deleteArticle(int $id, ArticleService $articleService, Request $request): RedirectResponse
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer un article.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }
    
        $article = $articleService->getOnearticle($id);
    
        // // Vérifier si l'utilisateur actuellement authentifié est le propriétaire de l'article
        $currentUser = $this->getUser();
        if ($currentUser !== null && $article->getRef() !== $currentUser->getId()) {
            // Si l'utilisateur n'est pas le propriétaire de l'article, afficher un message d'erreur
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cet article.');
            return $this->redirectToRoute('app_article'); // Rediriger vers la page d'articles
        }
    
        // Supprimer l'article
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
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier un article.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }
    
        $article = $articleService->getOnearticle($id);
    
        // Vérifier si l'utilisateur actuellement authentifié est le propriétaire de l'article
        $currentUser = $this->getUser();
        if ($currentUser !== null && $article->getRef() !== $currentUser->getId()) {
            // Si l'utilisateur n'est pas le propriétaire de l'article, afficher un message d'erreur
            // throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet article.');
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cet article.');
            return $this->redirectToRoute('app_article');
        }
    
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
