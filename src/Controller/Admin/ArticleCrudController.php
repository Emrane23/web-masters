<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\HttpFoundation\Request;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // $ApproaveArticle = Action::new('ApproveArticle', 'Change State')
        //     ->linkToCrudAction('ApproveArticle', function(Article $article): array {
        //         return [
        //             'id' => $article->getId()
        //         ];
        //     })->displayAsLink();
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        // ->add(Crud::PAGE_INDEX, $ApproaveArticle)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ->disable(Action::NEW)
        ->addBatchAction(Action::new('approve', 'Approve Articles')
        ->linkToCrudAction('approveArticles')
        ->addCssClass('btn btn-primary')
        ->setIcon('fa fa-check'))
        
        ;
    
    }
    

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Articles')
            ->setDefaultSort(['createdAt' => 'DESC'])
            // ->setFilters()
            ->setPaginatorPageSize(5)
            // ->showEntityActionsInlined()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('title')
            ->add('id')
            ->add('description')
            ->add('imageName')
            ->add('approved')
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title')/* ->setRequired(false) */,
            ImageField::new('imageName',"Image")->setBasePath('images/articles')->setUploadDir('public/images/articles'),
            TextareaField::new('description')->hideOnIndex(),
            AssociationField::new('category')->autocomplete(),
            AssociationField::new('user','Created by')->renderAsNativeWidget(),
            BooleanField::new('approved')/* ->renderAsSwitch(false) */,
            DateField::new('createdAt')->hideOnForm(),
        ];
    }

    public function ApproveArticles(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);
        foreach ($batchActionDto->getEntityIds() as $id) {
            $article = $entityManager->find($className, $id);
            $article->setApproved(!$article->isApproved());
        }
        $this->addFlash(
            'success',
            'Article(s) approved successfully!'
        );
        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    // public function ApproveArticle(AdminContext $context, ArticleRepository $articleRepository) 
    // {
    //     $article = $context->getEntity()->getInstance();
    //     $article->setApproved(!$article->isApproved()) ;
    //     $articleRepository->save($article,true);
    //     $context->getRequest()->getSession()->getFlashBag()->add(
    //         'success',
    //         'Article approved successfully!'
    //      );
    //     return $this->redirect($context->getReferrer());
    // }
    
}
