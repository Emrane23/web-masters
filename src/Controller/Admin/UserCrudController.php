<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        // ->add(Crud::PAGE_INDEX, Action::DETAIL)
        // ->disable(Action::NEW)
        // ->remove(Crud::PAGE_INDEX, Action::EDIT)
        // ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        // ->addBatchAction(Action::new('markasseen', 'Mark as seen\not seen')
        // ->linkToCrudAction('MarkAsSeen')
        // ->addCssClass('btn btn-primary')
        // ->setIcon('fa fa-check'))
        ;
    
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
