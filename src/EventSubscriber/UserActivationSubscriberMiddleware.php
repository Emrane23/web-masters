<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class UserActivationSubscriberMiddleware implements EventSubscriberInterface
{
    public $security ;
    public $urlGenerator ;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator) {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }
    
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        // Define routes that require user activation
        $routesRequiringActivation = [
            'app_article_create', // Adjust route names
            'app_article_editmyarticle',
            'app_article_edit',
            'app_article_delete',
        ];

        // Check if the requested route requires activation check
        $routeName = $request->attributes->get('_route');
        if (in_array($routeName, $routesRequiringActivation) && $user !== null && !$user->isActive()) {
            $homeUrl = $this->urlGenerator->generate('app_article'); // Adjust route name

            // Set the Location header to redirect to a specific route
            $response = new RedirectResponse($homeUrl);

            $request->getSession()->getFlashBag()->add(
                'notice',
                array('type' => 'danger', 'url' => null, 'message' => "Sorry your account is disabled by admin, so you can't do this action."),
             );
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
