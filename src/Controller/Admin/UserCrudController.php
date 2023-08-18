<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Users')
            ->setDefaultSort(['createdAt' => 'DESC'])
            // ->setFilters()
            ->setPaginatorPageSize(5)
            // ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $DisableUser = Action::new('DisableUser', 'Enable\Disable User')
            ->linkToCrudAction('enableDisableUser', function(User $article): array {
                return [
                    'id' => $article->getId()
                ];
            })->displayAsLink();
        return $actions
        ->add(Crud::PAGE_INDEX, $DisableUser)
        ->add(Crud::PAGE_DETAIL, $DisableUser)
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

    
    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('email'),
            BooleanField::new('disabled')->renderAsSwitch(false),
            BooleanField::new('isVerified')->renderAsSwitch(false),
            DateField::new('disabledAt')->hideOnForm(),
            DateField::new('createdAt')->hideOnForm(),
        ];
    }

    public function enableDisableUser(AdminContext $context, UserRepository $userRepository)
    {
        $user = $context->getEntity()->getInstance();
        if (in_array("ROLE_ADMIN", $user->getRoles())) { 
            $context->getRequest()->getSession()->getFlashBag()->add(
                'danger',
                "Sorry, you can't disable admin account!"
             );
            return $this->redirect($context->getReferrer());
             
        }
        $user->setDisabled(!$user->isDisabled()) ;
        if ($user->isDisabled()) {
            $user->setDisabledAt(new \DateTime()) ;
        }else{
            $user->setDisabledAt(null) ;
        }
        $userRepository->save($user,true);
        return $this->redirect($context->getReferrer());
    }
    
}
