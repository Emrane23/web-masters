<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

class ArticleController extends AbstractController
{

    #[Route('/', name: 'app_article', methods: "GET")]
    public function index(ArticleRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        // $expressionBuilder = $em->getExpressionBuilder();
        // $criteria = $expressionBuilder->eq('approved', true);
        // $articles = $paginator->paginate($repo->findBy(['approved' => $criteria], ['createdAt' => 'DESC']), 
        
        $expressionBuilder = Criteria::expr();
        $criteria = Criteria::create()->where($expressionBuilder->eq('approved', true))->orderBy(['createdAt' => Criteria::DESC]);
        // $articles = $paginator->paginate($repo->findBy(['approved' => $criteria], ['createdAt' => 'DESC']),
        $articles = $paginator->paginate($repo->matching($criteria),
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('article/index.html.twig', compact('articles'));
    }

    #[Route('/my-articles', methods: "GET")]
    #[IsGranted('ROLE_USER')]
    public function myArticles(ArticleRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {   
        // $expressionBuilder = Criteria::expr();
        // $criteria = Criteria::create()->where($expressionBuilder->eq('user',$this->getUser()))->orderBy(['createdAt' => Criteria::DESC]);
        // $articles = $paginator->paginate($repo->matching($criteria),

        $articles = $paginator->paginate($repo->findBy(['user' => $user = $this->getUser()], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('article/my_article.html.twig', compact('articles'));
    }

    #[Route('/articles/{id<\d+>}', methods: ["GET", "POST"])]
    public function show(Request $request, Article $article, CommentRepository $repo)
    {
        if (!$article->isApproved()) {
            throw $this->createNotFoundException('The article #' . $article->getId() . ' does not exist');
        }
        $comment = new Comment;
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setArticle($article);
            $repo->save($comment, true);
            $html = $this->renderView('article/_comment.html.twig', ['comment' => $comment]);

            return new JsonResponse(['success' => true, 'html' => $html]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->getFormErrors($form);

            return new JsonResponse(['errors' => $errors], 400);
        }
        return $this->render('article/show.html.twig', ['article' => $article, 'form' => $form->createView()]);
    }

    #[Route('/articles/create', methods: ["GET", "POST"])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, ArticleRepository $repo): Response
    {
        $article = new Article;
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUser($this->getUser());
            $repo->save($article, true);
            $url = $this->generateUrl('app_article_show', ['id' => $article->getId()]);
            $this->addFlash(
                'notice',
                array('type' => 'success', 'url' => $url, 'message' => 'successfully created'),
            );
            return $this->redirectToRoute('app_article');
        }
        return $this->render('article/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/articles/{id<\d+>}/edit', methods: ["GET", "POST"])]
    // #[Security("article.isApproved()")]
    #[IsGranted('ARTICLE_MANAGE', subject: "article")]
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        if (!$article->isApproved()) {
            throw $this->createNotFoundException('The article #' . $article->getId() . ' does not exist');
        }
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
            $url = $this->generateUrl('app_article_show', ['id' => $article->getId()]);
            $this->addFlash(
                'notice',
                array('type' => 'success', 'url' => $url, 'message' => 'successfully updated'),
            );
            return $this->redirectToRoute('app_article');
        }
        return $this->render('article/edit.html.twig', ['form' => $form->createView(), 'article' => $article]);
    }


    #[Route('/articles/my-blog/edit', methods: ["POST"])]
    public function editar(Request $request,ValidatorInterface $validator, EntityManagerInterface $em, UploadHandler $uploadHandler): Response
    {
        $articleId = $request->request->get('id');
        $article = $em->getRepository(Article::class)->find($articleId);
        $article->setTitle($request->request->get('title'));
        $article->setDescription($request->request->get('description'));
        $file = $request->files->get('image');

        if ($file) {
            $article->setImageFile($file);
            $uploadHandler->upload($article, 'imageFile');
        }

        $errors = $validator->validate($article);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        
            $em->persist($article);
            $em->flush();
            $url = $this->generateUrl('app_article_show', ['id' => $article->getId()]);
            $this->addFlash(
                'notice',
                array('type' => 'success', 'url' => $url, 'message' => 'successfully updated'),
            );
            return $this->redirectToRoute('app_article_myarticles');
    
    }

    #[Route('/articles/{id<\d+>}/delete', methods: ["POST"])]
    #[IsGranted('ARTICLE_MANAGE', subject: "article")]
    public function delete(Article $article, ArticleRepository $repo, Request $request): Response
    {
        if ($this->isCsrfTokenValid('article_delete_' . $article->getId(), $request->request->get('csrf_token'))) {
            $repo->remove($article, true);
            $this->addFlash(
                'notice',
                array('type' => 'info', 'url' => null, 'message' => 'Article successfully deleted'),
            );
        }
        return $this->redirectToRoute('app_article');
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return $errors;
    }
}
