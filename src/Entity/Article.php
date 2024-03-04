<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;
    

    #[ORM\Column]
    private ?int $ref = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRef(): ?int
    {
        return $this->ref;
    }

    public function setRef(int $ref): static
    {
        $this->ref = $ref;

        return $this;
    }
    
    public function articleId(int $id, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $test = $entityManager->getRepository(Article::class)->findOneBy(['ref' => $id]);
        // Vous pouvez effectuer d'autres opérations avec $test si nécessaire
    }
}
