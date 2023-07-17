<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    #[Route('/', name: 'app_article',methods:"GET")]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('article/index.html.twig',compact('articles'));
    }
    
    #[Route('/articles/{id<\d+>}',methods:"GET")]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig',compact('article'));
    }
    
    #[Route('/articles/create',methods:["GET","POST"])]
    public function create(Request $request): Response
    {
        return $this->render('article/create.html.twig');
    }


}
