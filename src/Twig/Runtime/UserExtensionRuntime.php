<?php

namespace App\Twig\Runtime;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\RuntimeExtensionInterface;

class UserExtensionRuntime implements RuntimeExtensionInterface
{
    public $security ;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    public function isUserActive(): bool
    {
        $user = $this->security->getUser();
        if ($user == null ) {
            return true ;
        }
        if ($user !== null && !$user->isDisabled()){
            return true ;
        }else{
            return false ;
        };
    }
}
