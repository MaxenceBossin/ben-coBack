<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractController
{
    // Fonction d'ajout des bennes verres
    #[Route('/register', name: 'register')]
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
        return $this->json(strlen($data->password));
        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $data->password
            )
        );
        if($data->roles == null ||
        $data->roles != 'ROLE_USER' ||
        $data->roles != 'ROLE_GARBAGECOLLECTOR' ||
        $data->roles != 'ROLE_ADMIN' ||
        
        !isset($data->roles)){
            $user->setRoles(["ROLE_USER"]);
        }else{
            $user->setRoles($data->role);
        } 
        if(isset($data->first_name)) $user->setFirstName($data->first_name);
        if(isset($data->last_name)) $user->setLastName($data->last_name);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json('Utilisateur inscrit');
    }


}