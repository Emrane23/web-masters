<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title')/* ->setRequired(false) */,
            TextareaField::new('description'),
            DateField::new('createdAt')->hideOnForm(),
            // TextEditorField::new('description'),
        ];
    }
    

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Categories')
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
            ->add('description')
        ;
    }

    // public function createEntity(string $entityFqcn)
    // {
    //     $product = new Category();
    //     $product->setTitle("test");

    //     return $product;
    // }

    public function configureActions(Actions $actions): Actions
    {
        // $sendInvoice = Action::new('sendInvoice', 'Send invoice', 'fa fa-envelope')
        //     // if the route needs parameters, you can define them:
        //     // 1) using an array
        //     ->linkToRoute('invoice_send', [
        //         'send_at' => (new \DateTime('+ 10 minutes'))->format('YmdHis'),
        //     ]);
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        // ->addBatchAction(Action::new('approve', 'Approve Users')
        //         ->linkToCrudAction('approveUsers')
        //         ->addCssClass('btn btn-primary')
        //         ->setIcon('fa fa-user-check'))
        ;
        // ->remove(Crud::PAGE_INDEX, Action::EDIT)
    
    }

}
