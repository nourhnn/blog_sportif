<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\Task\TaskService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task')]
    public function index(TaskService $service): Response
    {
        // dd($service->getAllTasks());

        return $this->render('task/index.html.twig', [
            'tasks' => $service->getAllTasks(),
            'defaultImg' => $this->getParameter('cover_default_img'),
        ]);
    }

    #[Route('/task/create', name: 'app_task_create')]
    public function createTask(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        $task = new Task();
        // on charge l'objet avec une dueDate:
        $task->setDueDate(new \DateTime('now'));

        // on charge l'objet avec une image
        $task->setCover($this->getParameter('cover_default_img'));


        $form = $this->createForm(TaskType::class, $task, [
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // on met a jour l'objet $task avec les données du formulaire
            $task = $form->getData();

            // on vérifie si le formulaire retourne une image
            // dans $_FILE
            $photoFile = $form->get('cover')->getData();

            // si il y a une image:
            if($photoFile) {
                $originalFileName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $slugger->slug($originalFileName);

                $newFileName = $safeFileName . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('cover_img_dir'),
                        $newFileName
                    );
                } catch(FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue: '. $e->getMessage());
                }

                $task->setCover($newFileName);
            }

            $doctrine->getManager()->persist($task);
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Nouvelle tâche ajoutée avec succès.');
            return $this->redirectToRoute('app_task');

        }
        return $this->render('task/create.html.twig', [
            'form' => $form,
            'img' => $task->getCover(),
        ]);
    }

    #[Route('/task/delete/{id}', name: 'app_task_delete')]
    public function deleteTask(int $id, TaskService $service): RedirectResponse {
        if($service->deleteOneTask($id)) {
            $this->addFlash('success', 'Tâche supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue.');
        }

        return $this->redirectToRoute('app_task');
    }

    #[Route('/task/update/{id}', name: 'app_task_update')]
    public function updateTask(int $id, Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine, TaskService $service): Response {
        $task = $service->getOneTask($id);
    
        // Sauvegarde de l'ancienne photo pour suppression ultérieure
        $oldPhotoPath = $task->getCover();
    
        // Créer le formulaire de mise à jour et le traiter
        $form = $this->createForm(TaskType::class, $task, [
            'method' => 'POST'
        ]);
    
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $task = $form->getData();
    
            // Vérifier si le formulaire contient une nouvelle image
            $photoFile = $form->get('cover')->getData();
    
            if($photoFile) {
                // Générer un nouveau nom de fichier sécurisé
                $originalFileName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $photoFile->guessExtension();
    
                try {
                    // Déplacer la nouvelle image vers le dossier cover_img
                    $photoFile->move(
                        $this->getParameter('cover_img_dir'),
                        $newFileName
                    );
    
                    // Mettre à jour le nom de fichier dans la tâche
                    $task->setCover($newFileName);
    
                    // Supprimer l'ancienne photo
                    if($oldPhotoPath) {
                        $oldPhotoFullPath = $this->getParameter('cover_img_dir') . '/' . $oldPhotoPath;
                        if(file_exists($oldPhotoFullPath)) {
                            unlink($oldPhotoFullPath);
                        }
                    }
                } catch(FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de la photo: '. $e->getMessage());
                }
            }
    
            // Enregistrer les modifications de la tâche dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
    
            $this->addFlash('success', 'Tâche modifiée avec succès.');
            return $this->redirectToRoute('app_task');
        }
    
        return $this->render('task/update.html.twig', [
            'form' => $form->createView(),
            'img' => $task->getCover(),
        ]);
    }    

}
