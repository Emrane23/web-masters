<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use App\Service\ObjectFiller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public $filler ;

    public function __construct(ObjectFiller $filler) {
        $this->filler = $filler;
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact ;
        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) { 
            // $data = $this->filler->toArray($contact) ;
            // $contact = $this->filler->fill($contact, $data);
            $contactRepository->save($contact,true);
            $this->addFlash(
                'notice',
                array('type' => 'success', 'url' => null, 'message' => 'Your message sended successfully!'),
            );
            return $this->redirectToRoute('app_article');
        }
        return $this->render('contact/index.html.twig', ['form' => $form->createView()]);
    }
}
