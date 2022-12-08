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
    #[Route('/ways', name: 'getWays', methods: ['GET'])]
    public function getWays(
        ManagerRegistry $doctrine
        ): JsonResponse {
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
    public function getWay(
        ManagerRegistry $doctrine,
        int $id): JsonResponse {
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

    #[Route('/way', name: 'add_way', methods: ['POST'])]
    public function addRoute(
        ManagerRegistry $doctrine,
        Request $request): JsonResponse {
        $data = json_decode($request->getContent());
        $dateStart = new DateTimeImmutable($data->date_start);
        $dateEnd = new DateTimeImmutable($data->date_end); 
        $routeJson = [$data->route_json];
        $teamJson =  [$data->team];
        if($dateStart->getTimestamp() >= $dateEnd->getTimestamp()) {
            return $this->json('Problème date', Response::HTTP_METHOD_NOT_ALLOWED);
        }
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

    #[Route('/way/{id}', name: 'update_way', methods: ['PUT'])]
    public function editWay(
        ManagerRegistry $doctrine, 
        Request $request, 
        int $id
        ): JsonResponse{
        /* Vérification ID */
        $way = $doctrine->getRepository(Way::class)->find($id);
        if($way == null){
            return $this->json('Error'); 
        }
        $data = json_decode($request->getContent());
        $entityManager = $doctrine->getManager();
        
        $data = json_decode($request->getContent());
        $dateStart = new DateTimeImmutable($data->date_start);
        $dateEnd = new DateTimeImmutable($data->date_end); 
        $routeJson = [$data->route_json];
        $teamJson =  [$data->team];
        if($dateStart->getTimestamp() >= $dateEnd->getTimestamp()) {
            return $this->json('Problème date');
        }

        $way->setDateStart($dateStart);
        $way->setDateEnd($dateEnd);
        $way->setRouteJson($routeJson);
        $way->setTeam($teamJson);

        $entityManager->persist($way);
        $entityManager->flush();

        return $this->json('Edition OK' );
    }

    #[Route('/way/{id}', name: 'delete_way', methods: ['DELETE'])]
    public function deleteWay(
        ManagerRegistry $doctrine,
        int $id)
        : JsonResponse{

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
