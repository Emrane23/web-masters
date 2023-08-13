<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Messages')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(5)
        ;
    }

    public function detail(AdminContext $context)
    {
        $contact = $context->getEntity()->getInstance();
        if (!$contact->isSeen()) {
            $contact->setSeen(true);
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
        }
        return parent::detail($context);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->disable(Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ->addBatchAction(Action::new('markasseen', 'Mark as seen\not seen')
        ->linkToCrudAction('MarkAsSeen')
        ->addCssClass('btn btn-primary')
        ->setIcon('fa fa-check'))
        ;
    
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('firstName'),
            TextField::new('lastName')->hideOnIndex(),
            TextField::new('subject'),
            TextField::new('email'),
            TextareaField::new('message'),
            BooleanField::new('seen')->renderAsSwitch(false),
        ];
    }

    public function MarkAsSeen(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);
        foreach ($batchActionDto->getEntityIds() as $id) {
            $contact = $entityManager->find($className, $id);
            $contact->setSeen(!$contact->isSeen());
        }
        $this->addFlash(
            'success',
            'Mark As read successfully!'
        );
        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
    
}
