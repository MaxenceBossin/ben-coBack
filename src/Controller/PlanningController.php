<?php

namespace App\Controller;

use App\Entity\Planning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PlanningRepository;
use DateTimeImmutable;

/**
 * @Route("/api", name="api_")
 */
class PlanningController extends AbstractController
{
    // Fonction d'ajout de planning
    #[Route('/addPlanning', name: 'app_addPlanning')]
    public function addPlanning(ManagerRegistry $doctrine, Request $request, PlanningRepository $planningRepo)
    {
        $entityManager = $doctrine->getManager();

        $dataArray = json_decode($request->getContent());
        
        foreach($dataArray as $data){
            
        $dateImmutable = new DateTimeImmutable($data->date);
        $dateCheck = $planningRepo->fetchWith1Date($dateImmutable);
        if ($dateCheck != null) {
            $planningRepo->replace($dateImmutable,$data->team);
        } else {
            
            $planning = new Planning();

            $planning->setTeam($data->team);
            $planning->setDate($dateImmutable);

            $entityManager->persist($planning);
            $entityManager->flush();
        
        }
    }
    return $this->json('Planning added !');


    }

    // Fonction d'uodate de planning
    // #[Route('/updatePlanning', name: 'app_updatePlanning')]
    // public function updatePlanning(ManagerRegistry $doctrine, Request $request)
    // {
    //     $entityManager = $doctrine->getManager();

    //     $data = json_decode($request->getContent());

    //     $teamJson = $data->teamJson;
    //     $date = $data->date;
    //     $dateImmutable = new DateTimeImmutable($date);

    //     $planningCheck = $doctrine->getRepository(Planning::class)->findOneBy(["date" => $dateImmutable]);

    //     $planningCheck->setTeam($teamJson);
    //     $planningCheck->setDate($dateImmutable);

    //     $entityManager->persist($planningCheck);
    //     $entityManager->flush();

    //     return $this->json('Planning modified !');
    // }


    // Fonction get planning
    #[Route('/getPlanning', name: 'getPlanning')]
    public function getPlanning(ManagerRegistry $doctrine, Request $request, PlanningRepository $planningRepo)
    {

        $data = json_decode($request->getContent());
        $date = new DateTimeImmutable($data->date);
        
        $res = $planningRepo->fetchWithDate($date);
        $i = 0;
        foreach ($res as $planning){
            $res[$i]['team'] = json_decode($res[$i]['team']);
            $i++;
        }
        return $this->json($res);
        // return $this->json([$date->  format('Y-m-d'), $dateEnd->format('Y-m-d')]);
    }
}
