<?php
// src/Service/VoteService.php

// src/Service/VoteService.php

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;

class VoteService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function vote(User $user, Article $article, $voteValue)
    {
        // Check if the user has already voted for this article
        $existingVote = $this->entityManager
            ->getRepository(Vote::class)
            ->findOneBy(['user' => $user, 'article' => $article]);

        $this->syncVotes($user, $article, $voteValue, $existingVote);
    }

    private function deleteVote(Vote $vote)
    {
        $this->entityManager->remove($vote);
        $this->entityManager->flush();
    }

    private function createVote(User $user, Article $article, $voteValue)
    {
        $vote = new Vote();
        $vote->setUser($user);
        $vote->setArticle($article);
        $vote->setVote($voteValue);
        $this->entityManager->persist($vote);
        $this->entityManager->flush();
    }

    private function syncVotes(User $user, Article $article, $voteValue,$existingVote)
    {
        if ($existingVote) {
            $this->deleteVote($existingVote);
        }
        $this->createVote($user, $article, $voteValue);
    }

    public function attachVotesToArticle(Article $article)
    {
        $votes = $this->entityManager
            ->getRepository(Vote::class)
            ->findBy(['article' => $article]);

        return $votes;
    }
}

