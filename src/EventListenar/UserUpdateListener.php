<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserUpdateListener
{
    public $request ;
    public function __construct(RequestStack $request) {
        $this->request = $request;
    }
    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        // Check if the 'disabled' field is changing
        if ($event->hasChangedField('disabled')) {
            // Get the old and new values
            $oldDisabledValue = $event->getOldValue('disabled');
            $newDisabledValue = $event->getNewValue('disabled');
            if (in_array("ROLE_ADMIN", $user->getRoles())) {
                die;
            }

            if ($newDisabledValue) {
                $user->setDisabledAt(new \DateTime()) ;
                $message = "Disable ".$user->fullName()." Account successfuly!";
            }else{
                $user->setDisabledAt(null) ;
                $message = "Enable ".$user->fullName()." Account successfuly!";
            }
            $this->request->getSession()->getFlashBag()->add(
                'success',
                "$message"
             );
        }
    }
}
