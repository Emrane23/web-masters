<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    #[Route('/', name: 'app_article',methods:"GET")]
    public function index(ArticleRepository $repo,  PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate($repo->findBy([], ['createdAt' => 'DESC']), 
        $request->query->getInt('page',1), 5) ;
        return $this->render('article/index.html.twig',compact('articles'));
    }
    
    #[Route('/articles/{id<\d+>}',methods:["GET","POST"])]
    public function show(Request $request, Article $article ,CommentRepository $repo)
    {
        $comment = new Comment ; 
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $comment->setUser($this->getUser());
            $comment->setArticle($article);
            $repo->save($comment,true);
            $html = $this->renderView('article/_comment.html.twig', ['comment' => $comment]);

        return new JsonResponse(['success' => true, 'html' => $html]);
        }
        return $this->render('article/show.html.twig',['article' => $article , 'form' =>$form->createView()]);
    }
    
    #[Route('/articles/create',methods:["GET","POST"])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, ArticleRepository $repo): Response
    {
        $article = new Article ;
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setUser($this->getUser());
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
    #[IsGranted('ARTICLE_MANAGE', subject:"article")]
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
