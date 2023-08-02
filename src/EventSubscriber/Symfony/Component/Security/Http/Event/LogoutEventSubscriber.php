<?php

namespace App\EventSubscriber\Symfony\Component\Security\Http\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    public function onLogoutEvent( LogoutEvent $event): void
    {
        $event->getRequest()->getSession()->getFlashBag()->add(
            'notice',
            array('type' => 'success', 'url' => null, 'message' => 'Logged out successfully !' ),
        )   ;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
