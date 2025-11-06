<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class UserLoginListener
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function onLoginSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        if ($user instanceof User) {
            $user->setDateDerniereConnexion(new \DateTimeImmutable());
            $this->entityManager->flush();
        }
    }
}