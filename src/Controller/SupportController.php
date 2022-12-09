<?php

namespace App\Controller;

use App\Entity\Dumpster;
use App\Entity\Support;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api", name="api_")
 */
class SupportController extends AbstractController
{
    // Fonction d'ajout des bennes verres
    #[Route('/addSupport', name: 'app_addSupport' , methods:['POST'])]
    public function add(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent());

        if(!empty($data->dumpsterId)) {$dumpsterId = $data->dumpsterId;} else { $dumpsterId = null;}
        $fkUserId = $data->fkUserId;
        $category = $data->category;
        $title = $data->title;
        if(!empty($data->imageSrc)) {$imageSrc = $data->imageSrc;} else { $imageSrc = null;}
        if(!empty($data->content)) {$content = $data->content;} else { $content = null;}
        
        
        $user = $doctrine->getRepository(User::class)->find($fkUserId);

        $support = new Support();
        if(!empty($dumpsterId)){
            $dumpster = $doctrine->getRepository(Dumpster::class)->find($dumpsterId);
            $support->setDumpster($dumpster);
        }
        
        $support->setFkUser($user);
        $support->setCategory($category);
        $support->setTitle($title);
        $support->setImageSrc($imageSrc);
        $support->setStatus("En attente");
        $support->setContent($content);

        $entityManager->persist($support);
        $entityManager->flush();

        return $this->json("ajout OK",  Response::HTTP_OK);
    }

    #[Route('/supports', name: 'getAllSupport' , methods:['GET'])]
    public function getAll(ManagerRegistry $doctrine): JsonResponse
    {
        $supports = $doctrine->getRepository(Support::class)->findAll();
        $data = [];
        foreach ($supports as $support) {
            if(!empty($support->getFkAdmin())){
                $idAdmin = $support->getFkAdmin()->getId();
                $nameAdmin = $support->getFkAdmin()->getFirstName() .' '.  $support->getFkAdmin()->getLastName();
            }else{
                $idAdmin = null;
                $nameAdmin = null;
            }
            if(!empty($support->getDumpster())){
                $id = $support->getDumpster()->getId();
                $latitude = $support->getDumpster()->getLatitude();
                $longitude = $support->getDumpster()->getLongitude();
                $type = $support->getDumpster()->getType();

                $streetNumber = $support->getDumpster()->getStreetNumber() ? $support->getDumpster()->getStreetNumber() : null ;
                $streetLabel = $support->getDumpster()->getStreetLabel() ? $support->getDumpster()->getStreetLabel() : null;
                $streetCity = $support->getDumpster()->getCity() ? $support->getDumpster()->getCity() : null;
                $codePostal = $support->getDumpster()->getPostalCode() ? $support->getDumpster()->getPostalCode() : null;
            }else{
                $id = null;
                $latitude = null;
                $longitude = null;
                $type = null;
                $streetNumber = null;
                $streetLabel =null;
                $streetCity = null;
                $codePostal = null;
            }
            $data[] = [
                "id" => $support->getId(),
                "dumpster_id" => $id,
                "dumpster_latitude" => $latitude,
                "dumpster_longitude" => $longitude,
                "dumpster_type" => $type ,
                "dumpster_street_number" => $streetNumber,
                "dumpster_street_label" => $streetLabel,
                "dumpster_street_city" => $streetCity ,
                "dumpster_cp" => $codePostal,
                "garbageCollector_id" => $support->getFkUser()->getId(),
                "garbageCollector_name" => $support->getFkUser()->getFirstName() .' '.  $support->getFkUser()->getLastName() ,
                "admin_id" => $idAdmin,
                "admin_name" =>$nameAdmin,
                "category" => $support->getCategory(),
                "title" => $support->getTitle(),
                "img" => $support->getImageSrc(),
                "content" => $support->getContent(),
                "status" => $support->getStatus(),

            ];
        }
        return $this->json($data , Response::HTTP_OK);
    }
    #[Route('/support/{id}', name: 'getOneSupport' , methods:['GET'])]
    public function getOne(ManagerRegistry $doctrine , int $id): JsonResponse
    {
        $support = $doctrine->getRepository(Support::class)->find($id);

        if(!empty($support->getFkAdmin())){
            $idAdmin = $support->getFkAdmin()->getId();
            $nameAdmin = $support->getFkAdmin()->getFirstName() .' '.  $support->getFkAdmin()->getLastName();
        }else{
            $idAdmin = null;
            $nameAdmin = null;
        }
        if(!empty($support->getDumpster())){
            $id = $support->getDumpster()->getId();
            $latitude = $support->getDumpster()->getLatitude();
            $longitude = $support->getDumpster()->getLongitude();
            $type = $support->getDumpster()->getType();

            $streetNumber = $support->getDumpster()->getStreetNumber() ? $support->getDumpster()->getStreetNumber() : null ;
            $streetLabel = $support->getDumpster()->getStreetLabel() ? $support->getDumpster()->getStreetLabel() : null;
            $streetCity = $support->getDumpster()->getCity() ? $support->getDumpster()->getCity() : null;
            $codePostal = $support->getDumpster()->getPostalCode() ? $support->getDumpster()->getPostalCode() : null;
        }else{
            $id = null;
            $latitude = null;
            $longitude = null;
            $type = null;
            $streetNumber = null;
            $streetLabel =null;
            $streetCity = null;
            $codePostal = null;
        }
        $data = [
            "id" => $support->getId(),
            "dumpster_id" => $id,
            "dumpster_latitude" => $latitude,
            "dumpster_longitude" => $longitude,
            "dumpster_type" => $type ,
            "dumpster_street_number" => $streetNumber,
            "dumpster_street_label" => $streetLabel,
            "dumpster_street_city" => $streetCity ,
            "dumpster_cp" => $codePostal,
            "garbageCollector_id" => $support->getFkUser()->getId(),
            "garbageCollector_name" => $support->getFkUser()->getFirstName() .' '.  $support->getFkUser()->getLastName() ,
            "admin_id" => $idAdmin,
            "admin_name" =>$nameAdmin,
            "category" => $support->getCategory(),
            "title" => $support->getTitle(),
            "img" => $support->getImageSrc(),
            "content" => $support->getContent(),
            "status" => $support->getStatus(),

        ];

        return $this->json($data , Response::HTTP_OK );
    }

    #[Route('/support/{id}', name: 'changeSupportStatus' , methods:['PATCH'])]
    public function changeStatus(ManagerRegistry $doctrine,  Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $status = json_decode($request->getContent());
        $support = $doctrine->getRepository(Support::class)->find($id);
        if($support == null){
            return $this->json('Error' ,Response::HTTP_BAD_REQUEST); 
        }

        $support->setStatus($status->status);
        $entityManager->persist($support);
        $entityManager->flush();

        return $this->json($status , Response::HTTP_OK );
    }
}
