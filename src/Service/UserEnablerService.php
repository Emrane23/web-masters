<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserEnablerService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function enableUsers()
    {
        date_default_timezone_set('Africa/Tunis');
        $usersToDisable = $this->entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.disabled = true')
            ->andWhere('u.disabledAt <= :timestamp')
            ->setParameter('timestamp', new \DateTimeImmutable('-1 minute'))
            ->getQuery()
            ->getResult();;

        foreach ($usersToDisable as $user) {
            $user->setDisabled(false);
            $user->setDisabledAt(null);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
    }
}
