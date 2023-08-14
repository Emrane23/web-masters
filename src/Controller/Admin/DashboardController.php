<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineExtensions\Query\Mysql\DateFormat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        $repository  = $this->getDoctrine()->getRepository(Article::class);
        $postData = $repository->createQueryBuilder('p')
            ->select("DATE_FORMAT(p.createdAt, '%Y-%m-%d') as date, COUNT(p.id) as count")
            ->groupBy('date')
            ->getQuery()
            ->getResult();

        $pendingArticle = count($repository->findBy(['approved' => false])) ;
        $totalArticles = 0;
            foreach ($postData as $item) {
                $totalArticles += $item['count'];
            }
        return $this->render('admin/dashboard.html.twig',compact('postData','totalArticles','pendingArticle'));
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Web Masters');
    }

    public function configureMenuItems(): iterable
    {
        $numNotSeenContact = $this->pendingContact();
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Articles', 'fa fa-newspaper-o', Article::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-list-alt', Category::class);
        yield MenuItem::linkToCrud("Contacts $numNotSeenContact", 'fa fa-envelope', Contact::class);
        yield MenuItem::linkToCrud("Users", 'fa fa-users', User::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
        ->setName($user->getFirstName())
        ->displayUserName(true)
        // ->addMenuItems([
        //     MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
        //     MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
        //     MenuItem::section(),
        // ])
        ;
    }

    private function pendingContact()
    {
        $repository  = $this->getDoctrine()->getRepository(Contact::class);
        $notSeenContact = $repository->findBy(['seen' => false]) ;
        $numNotSeenContact = count($notSeenContact);
        if ($numNotSeenContact == 0) {
            return '' ;
        }
        $result =  $numNotSeenContact > 100 ? '99+' : $numNotSeenContact ;
        return "<span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>
        $result
        <span class='visually-hidden'>unread messages</span>
      </span>" ;
    }
}
