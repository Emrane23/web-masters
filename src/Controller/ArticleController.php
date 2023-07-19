<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    
    #[Route('/articles/{id<\d+>}',methods:["GET","POST"])]
    public function show(Request $request, Article $article ,CommentRepository $repo)
    {
        $comment = new Comment ; 
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $comment->setArticle($article);
            // $repo->save($comment,true);
            return $this->json([
                'message' => 'Your comment added sucessfully!'
            ]);
        }
        return $this->render('article/show.html.twig',['article' => $article , 'form' =>$form->createView()]);
    }
    
    #[Route('/articles/create',methods:["GET","POST"])]
    public function create(Request $request, ArticleRepository $repo): Response
    {
        $article = new Article ;
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $repo->save($article,true);
            $url =$this->generateUrl('app_article_show', ['id' => $article->getId()]);
            $this->addFlash(
                'notice',
                array('type' => 'success', 'url' => $url, 'message' => 'successfully updated'),
             );
             return $this->redirectToRoute('app_article');
        }
        return $this->render('article/create.html.twig',['form' => $form->createView()]);
    }

    #[Route('/articles/{id<\d+>}/edit', methods:["GET","POST"])]
    public function edit(Request $request, Article $article , EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->persist($article);
            $em->flush();
            $url =$this->generateUrl('app_article_show', ['id' => $article->getId()]);
            $this->addFlash(
                'notice',
                array('type' => 'success', 'url' => $url, 'message' => 'successfully updated'),
             );
             return $this->redirectToRoute('app_article');
        }
        return $this->render('article/edit.html.twig',['form' => $form->createView(),'article' => $article]);
    }


}
