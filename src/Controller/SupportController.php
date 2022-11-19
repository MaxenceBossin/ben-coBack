<?php

namespace App\Controller;

use App\Entity\Dumpster;
use App\Entity\Support;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api", name="api_")
 */
class SupportController extends AbstractController
{
    // Fonction d'ajout des bennes verres
    #[Route('/addSupport', name: 'app_addSupport')]
    public function addSupport(ManagerRegistry $doctrine, Request $request)
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent());

        $dumpsterId = $data->dumpsterId;
        $fkUserId = $data->fkUserId;
        $category = $data->category;
        $title = $data->title;
        $imageSrc = $data->imageSrc;
        $content = $data->content;

        $dumpster = $doctrine->getRepository(Dumpster::class)->find($dumpsterId);
        $user = $doctrine->getRepository(User::class)->find($fkUserId);

        $support = new Support();

        $support->setDumpster($dumpster);
        $support->setFkUser($user);
        $support->setCategory($category);
        $support->setTitle($title);
        $support->setImageSrc($imageSrc);
        $support->setStatus("En attente");
        $support->setContent($content);

        $entityManager->persist($support);
        $entityManager->flush();

        // return $this->redirect('showMessage');
    }
}
