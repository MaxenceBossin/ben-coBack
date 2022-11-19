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
    // Fonction d'ajout des bennes verres
    #[Route('/addDumpsterVerre', name: 'app_addDumpsterVerre')]
    public function addDumpsterVerre(ManagerRegistry $doctrine, HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request(
            'GET',
            'https://data.toulouse-metropole.fr/api/records/1.0/search/?dataset=points-dapport-volontaire-dechets-et-moyens-techniques&q=&rows=10000&facet=commune&facet=details_source_geoloc&facet=details_source_attributaire&facet=type_flux&facet=prestataire&facet=centre_ville&facet=zone&refine.flux=R%C3%A9cup%27verre&refine.commune=Toulouse'
        );

        $benne = json_decode($response->getContent());
        $benneInfo = $benne->records;

        foreach ($benneInfo as $benneInfos) {
            $longitude = $benneInfos->geometry->coordinates[0];
            $latitude = $benneInfos->geometry->coordinates[1];
            $type = "verre";
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

    // Fonction d'ajout des bennes ordures ménagères
    #[Route('/addDumpsterOrdure', name: 'app_addDumpsterOrdure')]
    public function addDumpsterOrdure(ManagerRegistry $doctrine, HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request(
            'GET',
            'https://data.toulouse-metropole.fr/api/records/1.0/search/?dataset=points-dapport-volontaire-dechets-et-moyens-techniques&q=&facet=commune&rows=10000&facet=details_source_geoloc&facet=details_source_attributaire&facet=type_flux&facet=prestataire&facet=centre_ville&facet=zone&refine.type_flux=ordures+m%C3%A9nag%C3%A8res&refine.details_source_attributaire=G%C3%A9o+Propret%C3%A9&refine.commune=Toulouse'
        );

        $benne = json_decode($response->getContent());
        $benneInfo = $benne->records;

        foreach ($benneInfo as $benneInfos) {
            $longitude = $benneInfos->geometry->coordinates[0];
            $latitude = $benneInfos->geometry->coordinates[1];
            $type = "ordures ménagères";
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

    // Fonction d'ajout des bennes collectes sélectives
    #[Route('/addDumpsterCollecte', name: 'app_addDumpsterCollecte')]
    public function addDumpsterCollecte(ManagerRegistry $doctrine, HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request(
            'GET',
            'https://data.toulouse-metropole.fr/api/records/1.0/search/?dataset=points-dapport-volontaire-dechets-et-moyens-techniques&rows=10000&q=&facet=commune&facet=details_source_geoloc&facet=details_source_attributaire&facet=type_flux&facet=prestataire&facet=centre_ville&facet=zone&refine.details_source_attributaire=G%C3%A9o+Propret%C3%A9&refine.type_flux=collecte+s%C3%A9lective'
        );

        $benne = json_decode($response->getContent());
        $benneInfo = $benne->records;

        foreach ($benneInfo as $benneInfos) {
            $longitude = $benneInfos->geometry->coordinates[0];
            $latitude = $benneInfos->geometry->coordinates[1];
            $type = "collecte sélective";
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

    // Fonction d'ajout des bennes textiles
    #[Route('/addDumpsterTextile', name: 'app_addDumpsterTextile')]
    public function addDumpsterTextile(ManagerRegistry $doctrine, HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request(
            'GET',
            'https://data.toulouse-metropole.fr/api/records/1.0/search/?dataset=points-dapport-volontaire-dechets-et-moyens-techniques&rows=10000&q=&facet=commune&facet=details_source_geoloc&facet=details_source_attributaire&facet=type_flux&facet=prestataire&facet=centre_ville&facet=zone&refine.details_source_attributaire=G%C3%A9o+Propret%C3%A9&refine.type_flux=textile'
        );

        $benne = json_decode($response->getContent());
        $benneInfo = $benne->records;

        foreach ($benneInfo as $benneInfos) {
            $longitude = $benneInfos->geometry->coordinates[0];
            $latitude = $benneInfos->geometry->coordinates[1];
            $type = "textile";
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
