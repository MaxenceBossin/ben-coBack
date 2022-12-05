<?php

namespace App\Controller;
use App\Entity\Way;
use DateTimeImmutable;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @Route("/api", name="api_")
 */
class WayController extends AbstractController
{
    private $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    #[Route('/way', name: 'add_way', methods: ['POST'])]
    public function addRoute(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $dateStart = new DateTimeImmutable($data->date_start);
        $dateEnd = new DateTimeImmutable($data->date_end); 
        $routeJson = [$data->route_json];
        $teamJson =  [$data->team];
        /* Verification des dates */
        if($dateStart->getTimestamp() >= $dateEnd->getTimestamp()) {
            return $this->json('Problème date', Response::HTTP_METHOD_NOT_ALLOWED);
        }
        /* TODO: vérifié qu'une date n'existe pas déjà */
        $entityManager = $doctrine->getManager();

        $route = new Way();
        $route->setDateStart($dateStart);
        $route->setDateEnd($dateEnd);
        $route->setRouteJson($routeJson);
        $route->setTeam($teamJson);

        $entityManager->persist($route);
        $entityManager->flush();
        return $this->json(['route créé', Response::HTTP_OK]);
    }

    #[Route('/ways', name: 'getWays', methods: ['GET'])]
    public function getWays(ManagerRegistry $doctrine): JsonResponse {
        $ways = $doctrine->getRepository(Way::class)->findAll();
        $data = [];
        foreach ($ways as $way) {
            $data[] = [
                "id" => $way->getId(),
                "team" => $way->getTeam(),
                "date_start" => $way->getDateStart(),
                "date_end" => $way->getDateEnd(),
                "route_json" => $way->getRouteJson()     
            ];
        }

        return $this->json($data);
    }


    #[Route('/way/{id}', name: 'getOneWay', methods: ['GET'])]
    public function getWay(ManagerRegistry $doctrine, int $id): JsonResponse {

        $way = $doctrine->getRepository(Way::class)->find($id);
        if($way == null){
            return $this->json('Error'); 
        }
        
        $data[] = [
            "id" => $way->getId(),
            "team" => $way->getTeam(),
            "date_start" => $way->getDateStart(),
            "date_end" => $way->getDateEnd(),
            "route_json" => $way->getRouteJson()
        ];

        return $this->json($data);

    }

    #[Route('/deleteWay/{id}', name: 'delete_way', methods: ['DELETE'])]
    public function deleteWay(ManagerRegistry $doctrine, int $id): JsonResponse{

        $entityManager = $doctrine->getManager();

        $way = $doctrine->getRepository(Way::class)->find($id);
        if($way == null){
            return $this->json('Error'); 
        }

        $entityManager->remove($way);
        $entityManager->flush();

        return $this->json('Suppression OK');

    }
}
