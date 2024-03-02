<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\Article\ArticleService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleService $service): Response
    {
        // dd($service->getAllArticles());

        return $this->render('article/index.html.twig', [
            'articles' => $service->getAllArticles(),
            'defaultImg' => $this->getParameter('cover_default_img'),
        ]);
    }

    #[Route('/article/create', name: 'app_article_create')]
    public function createArticle(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // on met a jour l'objet $article avec les données du formulaire
            $article = $form->getData();

            $doctrine->getManager()->persist($article);
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Nouvelle tâche ajoutée avec succès.');
            return $this->redirectToRoute('app_article');

        }
        return $this->render('article/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function deleteArticle(int $id, ArticleService $service): RedirectResponse {
        if($service->deleteOneArticle($id)) {
            $this->addFlash('success', 'Tâche supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue.');
        }

        return $this->redirectToRoute('app_article');
    }

    #[Route('/article/update/{id}', name: 'app_article_update')]
    public function updateArticle(int $id, Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine, ArticleService $service): Response {
        $article = $service->getOneArticle($id);
    
        // Sauvegarde de l'ancienne photo pour suppression ultérieure
        $oldPhotoPath = $article->getCover();
    
        // Créer le formulaire de mise à jour et le traiter
        $form = $this->createForm(ArticleType::class, $article, [
            'method' => 'POST'
        ]);
    
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $article = $form->getData();
    
            // Enregistrer les modifications de la tâche dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
    
            $this->addFlash('success', 'Tâche modifiée avec succès.');
            return $this->redirectToRoute('app_article');
        }
    
        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }    

}
