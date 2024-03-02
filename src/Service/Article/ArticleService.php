<?php
namespace App\Service\Article;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

class ArticleService
{
    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupération de toutes les tâches
     *
     * @return Article[] return an array of Article objects
     */
    public function getAllArticles(): array {
        return $this->doctrine->getManager()->getRepository(Article::class)->findBy([], ['dueDate' => 'ASC']);
    }

    public function deleteOneArticle(int $id): bool {
        $article = $this->doctrine->getManager()->getRepository(Article::class)->findOneBy(['id' => $id]);

        try {
            $this->doctrine->getManager()->remove($article);
            $this->doctrine->getManager()->flush();
        } catch(Exception $e) {
            return false;
        }
        return true;
    }
    
    public function getOneArticle(int $id){
        return $this->doctrine->getManager()->getRepository(Article::class)->findOneBy(['id' => $id]);
    }
    
}