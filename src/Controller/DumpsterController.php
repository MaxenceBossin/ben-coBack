<?php

namespace App\Controller;

use App\Entity\Dumpster;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/api", name="api_")
 */
class DumpsterController extends AbstractController
{
    // Fonction d'ajout de toutes les bennes
    #[Route('/addDumpster', name: 'app_addDumpster')]
    public function addDumpster(ManagerRegistry $doctrine, HttpClientInterface $httpClient): Response
    {
        ini_set('max_execution_time', 300);

        $response = $httpClient->request(
            'GET',
            'https://data.toulouse-metropole.fr/api/records/1.0/search/?dataset=points-dapport-volontaire-dechets-et-moyens-techniques&q=&rows=10000&facet=commune&facet=details_source_geoloc&facet=details_source_attributaire&facet=type_flux&facet=prestataire&facet=centre_ville&facet=zone&refine.flux=R%C3%A9cup%27verre&refine.commune=Toulouse'
        );

        $benne = json_decode($response->getContent());
        $benneInfo = $benne->records;

        foreach ($benneInfo as $benneInfos) {
            $longitude = $benneInfos->geometry->coordinates[0];
            $latitude = $benneInfos->geometry->coordinates[1];
            $type = $benneInfos->fields->type_flux;
            $commune = $benneInfos->fields->commune;
            $voie = (isset($benneInfos->fields->voie)) ? ($benneInfos->fields->voie) : null;
            $numeroVoie = (isset($benneInfos->fields->numero_voie)) ? ($benneInfos->fields->numero_voie) : null;
            $codePostal = (isset($benneInfos->fields->code_postal)) ? ($benneInfos->fields->code_postal) : null;

            $entityManager = $doctrine->getManager();

            $dumpster = new Dumpster();

            $dumpster->setLongitude($longitude);
            $dumpster->setLatitude($latitude);
            $dumpster->setType($type);
            $dumpster->setCapacity(0);
            $dumpster->setCity($commune);
            $dumpster->setStreetLabel($voie);
            $dumpster->setStreetNumber($numeroVoie);
            $dumpster->setPostalCode($codePostal);
            $entityManager->persist($dumpster);
            $entityManager->flush();
        }

        return $this->redirect('../api/showDumpster');
    }

    // Fonction d'affichage de toutes les bennes
    #[Route('/showDumpster', name: 'app_showDumpster')]
    public function showDumpster(ManagerRegistry $doctrine): Response
    {
        $dumpster = $doctrine->getRepository(Dumpster::class)->findAll();

        $tab = [];

        foreach ($dumpster as $dumpsters) {
            $tab[] = [
                "id" => $dumpsters->getId(),
                "latitude" => $dumpsters->getLatitude(),
                "longitude" => $dumpsters->getLongitude(),
                "type" => $dumpsters->getType(),
                "capacity" => $dumpsters->getCapacity(),
                "city" => $dumpsters->getCity(),
                "street_label" => $dumpsters->getStreetLabel(),
                "street_number" => $dumpsters->getStreetNumber(),
                "postal_code" => $dumpsters->getPostalCode()
            ];
        }
        return $this->json($tab);
    }

    // Fonction d'affichage d'une seule benne
    #[Route('/showOneDumpster/{id}', name: 'app_showOneDumpster')]
    public function showOneDumpster(ManagerRegistry $doctrine, int $id)
    {
        $dumpster = $doctrine->getRepository(Dumpster::class)->find($id);

        $tab[] = [
            "id" => $dumpster->getId(),
            "latitude" => $dumpster->getLatitude(),
            "longitude" => $dumpster->getLongitude(),
            "type" => $dumpster->getType(),
            "capacity" => $dumpster->getCapacity(),
            "city" => $dumpster->getCity(),
            "street_label" => $dumpster->getStreetLabel(),
            "street_number" => $dumpster->getStreetNumber(),
            "postal_code" => $dumpster->getPostalCode()
        ];

        return $this->json($tab);
    }

    // Fonction de suppression d'une seule benne
    #[Route('/deleteDumpster/{id}', name: 'app_deleteDumpster')]
    public function deleteDumpster(ManagerRegistry $doctrine, int $id)
    {
        $entityManager = $doctrine->getManager();

        $dumpster = $doctrine->getRepository(Dumpster::class)->find($id);

        $entityManager->remove($dumpster);
        $entityManager->flush();

        return $this->redirect('../api/showDumpster');
    }
}
