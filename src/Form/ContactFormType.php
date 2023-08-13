<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('subject',ChoiceType::class, [
                'choices' => [
                    '--Select Your Issue--' => '',
                    'Request Invoice for order' => 'invoice_request',
                    'Request order status' => 'order_status_request',
                    'Haven\'t received cashback yet' => 'cashback_issue',
                    'Other' => 'other',
                ],
                    'required' => true,
                    'label' => 'Please specify your need',
                    'placeholder' => '--Select Your Issue--',
            ])
            ->add('message')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
