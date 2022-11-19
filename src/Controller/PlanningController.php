<?php

namespace App\Controller;

use App\Entity\Planning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use DateTimeImmutable;

/**
 * @Route("/api", name="api_")
 */
class PlanningController extends AbstractController
{
    // Fonction d'ajout de planning
    #[Route('/addPlanning', name: 'app_addPlanning')]
    public function addPlanning(ManagerRegistry $doctrine, Request $request)
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent());

        $teamJson = $data->teamJson;
        $date = $data->date;
        $dateImmutable = new DateTimeImmutable($date);

        $dateCheck = $doctrine->getRepository(Planning::class)->findOneBy(["date" => $dateImmutable]);
        
        if ($dateCheck != null) {
            return $this->updatePlanning($doctrine, $request);
        } else {
            $planning = new Planning();

            $planning->setTeam($teamJson);
            $planning->setDate($dateImmutable);

            $entityManager->persist($planning);
            $entityManager->flush();
            return $this->json('Planning added !');
        }
    }

    // Fonction d'uodate de planning
    #[Route('/updatePlanning', name: 'app_updatePlanning')]
    public function updatePlanning(ManagerRegistry $doctrine, Request $request)
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent());

        $teamJson = $data->teamJson;
        $date = $data->date;
        $dateImmutable = new DateTimeImmutable($date);

        $planningCheck = $doctrine->getRepository(Planning::class)->findOneBy(["date" => $dateImmutable]);

        $planningCheck->setTeam($teamJson);
        $planningCheck->setDate($dateImmutable);

        $entityManager->persist($planningCheck);
        $entityManager->flush();

        return $this->json('Planning modified !');
    }
}
