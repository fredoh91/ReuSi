<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AjoutManuelUserController extends AbstractController{


    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }


    #[Route('/cree_hash_pass', name: 'app_cree_hash_pass')]
    public function creeHashPassFromClairPass(): Response
    {        

        
        // $entityManager = $doctrine->getManager();
        // $Susar = $entityManager->getRepository(SusarEU::class)->findSusarByMasterId($master_id);
        $Users = $this->entityManager->getRepository(User::class)->findByPassEnClair();
        $dateCreation = new \DateTimeImmutable();

        foreach ($Users as $user) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPasswordEnClair()));
            $user->setDateCreation($dateCreation);
            $user->setPasswordEnClair(null);
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
        // dump($Users);
        return $this->render('ajout_manuel_user/cree_hash_pass.html.twig', [
            'controller_name' => 'AjoutManuelUserController',
            'Users' => $Users,

        ]);
    }
}
