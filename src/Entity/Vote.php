<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'votes')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity:'User',inversedBy: 'votes')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity:'Article',inversedBy: 'votes')]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $vote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): static
    {
        $this->vote = $vote;

        return $this;
    }
}
