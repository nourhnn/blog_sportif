<?php
namespace App\Service\Task;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

class TaskService
{
    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupération de toutes les tâches
     *
     * @return Task[] return an array of Task objects
     */
    public function getAllTasks(): array {
        return $this->doctrine->getManager()->getRepository(Task::class)->findBy([], ['dueDate' => 'ASC']);
    }

    public function deleteOneTask(int $id): bool {
        $task = $this->doctrine->getManager()->getRepository(Task::class)->findOneBy(['id' => $id]);

        try {
            $this->doctrine->getManager()->remove($task);
            $this->doctrine->getManager()->flush();
        } catch(Exception $e) {
            return false;
        }
        return true;
    }
    
    public function getOneTask(int $id){
        return $this->doctrine->getManager()->getRepository(Task::class)->findOneBy(['id' => $id]);
    }
    
}