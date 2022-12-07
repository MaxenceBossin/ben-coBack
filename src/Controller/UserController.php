<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractController
{

    // Fonction d'ajout des bennes verres
    #[Route('/register', name: 'register',  methods: ['POST'])]
        public function addDumpsterOrdure(
            EntityManagerInterface $entityManager,
            Request $request,
            UserPasswordHasherInterface $passwordHasher,
            ): JsonResponse
    {
        $data = json_decode($request->getContent());

        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)){
            return throw $this->createNotFoundException('Mauvais format email');
        }
        if(strlen($data->password) < 5){
            return throw $this->createNotFoundException('Mauvais format de mots de passe');
        }

        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $data->password
            )
        );
        if($data->roles == null) {
            $user->setRoles(["ROLE_USER"]);
        }else{
            $user->setRoles($data->roles);
        } 
        if(isset($data->first_name)) $user->setFirstName($data->first_name);
        if(isset($data->last_name)) $user->setLastName($data->last_name);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json('Utilisateur inscrit');
    }

    #[Route('/showUsers', name: 'app_showUsers',  methods: ['GET'])]
    public function showUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();

        $tab = [];

        foreach ($users as $user) {
            $tab[] = [
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "roles" => $user->getRoles(),
                "first_name" => $user->getFirstName(),
                "last_name" => $user->getLastName(),
            ];
        }
        return $this->json($tab);
    }

    #[Route('/showGarbageCollector', name: 'app_GarbageCollector',  methods: ['GET'])]
    public function showGarbageCollector(ManagerRegistry $doctrine, UserRepository $userRepository): JsonResponse
    {
        $roles = ["ROLE_GARBAGE_COLLECTOR"];
        $users = $doctrine->getRepository(User::class)->findBy(['roles' => $roles]);

        $users = $userRepository->findUsers('ROLE_GARBAGE_COLLECTOR');
        $tab = [];

        foreach ($users as $user) {
            $tab[] = [
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "roles" => $user->getRoles(),
                "first_name" => $user->getFirstName(),
                "last_name" => $user->getLastName(),
            ];
        }
        return $this->json($tab);
    }

    #[Route('/setGarbageCollector', name: 'setGb' ,  methods: ['PUT', 'PATCH'])]
    public function setGarbageCollector(
        ManagerRegistry $doctrine,
        Request $request
    ): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        /* recup mail */
        $data = json_decode($request->getContent());
        $mail = $data->email;
        /* verif mail */
        $user = $doctrine->getRepository(User::class)->findOneBy(["email" => $mail]);
        if(empty($user)){
            return $this->json('mauvais mail');
        }
        $user->setRoles(["ROLE_GARBAGE_COLLECTOR"]);
        $newRole = $user->getRoles();
        /* changer le role */
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($newRole);
    }

    #[Route('/removeGarbageCollector/{id}', name: 'setUser' ,  methods: ['PATCH'])]
    public function removeGarbageCollector(
        ManagerRegistry $doctrine,
        Request $request,
        int $id
    ): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        /* verif mail */
        $user = $doctrine->getRepository(User::class)->find($id);
        if(empty($user)){
            return $this->json('mauvais mail');
        }
        $user->setRoles(["ROLE_USER"]);
        /* changer le role */
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json('modification ok');
    }


}