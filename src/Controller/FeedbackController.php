<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Vote;
use App\Repository\ArticleRepository;
use App\Repository\VoteRepository;
use App\Service\ObjectFiller;
use App\Service\VoteService;
use App\Twig\Extension\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    public $filler ;
    public $articleRepository ;
    public $voteService ;

    public function __construct(ObjectFiller $filler,VoteService $voteService, ArticleRepository $articleRepository) {
        $this->filler = $filler;
        $this->articleRepository = $articleRepository;
        $this->voteService = $voteService;
    }

    #[Route('/feedback/{id<\d+>}', name: 'app_feedback', methods:["POST"])]
    public function feedback(Request $request, Article $article): JsonResponse
    {
        $user = $this->getUser() ;
        if($this->isCsrfTokenValid($user->getId(), $request->request->get('_token'))){
            $this->voteService->vote($user, $article, $request->request->get('vote'));
            $averageVotes = $this->articleRepository->calculateAverageVotesForArticle($article);
            $totalVotes =  $this->articleRepository->calculateTotalVotesForArticle($article);
        }else{
            throw new HttpException(419, 'Authentication Timeout');
        }
        $review = AppExtension::pluralize($totalVotes, 'Review') ;
        return $this->json([
            'message' => 'Thanx for your feedback',
            'averageVotes' => $averageVotes,
            'review' => $review
        ]);
    }
}
