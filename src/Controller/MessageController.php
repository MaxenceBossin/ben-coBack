<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class MessageController extends AbstractController
{
    public $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    // Fonction d'ajout des bennes verres
    #[Route('/addMessage', name: 'app_addMessage')]
    public function addMessage(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent());
        $content = $data->content;
        $date = $data->date;
        $dateImmutable = new DateTimeImmutable($date);
        $senderId = $data->sender_id;
        $receiverId = $data->receiver_id;

        $sender = $doctrine->getRepository(User::class)->findOneBy(['id' => $senderId]);
        $receiver = $doctrine->getRepository(User::class)->findOneBy(['id' => $receiverId]);

        $entityManager = $doctrine->getManager();

        $message = new Message();

        $message->setContent($content);
        $message->setDate($dateImmutable);

        $message->setSender($sender);
        $message->setReceiver($receiver);
        $entityManager->persist($message);
        $entityManager->flush();

        return $this->redirect('showMessage');
    }

    // Fonction d'affichage de toutes les bennes
    #[Route('/showMessage', name: 'app_showMessage')]
    public function showMessage(ManagerRegistry $doctrine): Response
    {
        $message = $doctrine->getRepository(Message::class)->findAll();

        $tab = [];

        foreach ($message as $messages) {
            $tab[] = [
                "id" => $messages->getId(),
                "sender_id" => $messages->getSender(),
                "receiver_id" => $messages->getReceiver(),
                "content" => $messages->getContent(),
                "date" => $messages->getDate()
            ];
        }
        return $this->json($tab);
    }

    // Fonction d'affichage d'une conversation
    #[Route('/showConversation', name: 'app_showOneConversation', methods: ['GET'])]
    public function showOneConversation(Request $request)
    {
        $data = json_decode($request->getContent());

        $senderId = $data->sender_id;
        $receiverId = $data->receiver_id;

        $getConversations = $this->messageRepository->getConversation($senderId, $receiverId);

        $tab = [];

        foreach ($getConversations as $getConversation) {
            $tab[] = [
                "id" => $getConversation->getId(),
                "sender_email" => $getConversation->getSender()->getEmail(),
                "sender_first_name" => $getConversation->getSender()->getFirstName(),
                "sender_last_name" => $getConversation->getSender()->getLastName(),
                "receiver_email" => $getConversation->getReceiver()->getEmail(),
                "receiver_first_name" => $getConversation->getReceiver()->getFirstName(),
                "receiver_last_name" => $getConversation->getReceiver()->getLastName(),
                "content" => $getConversation->getContent(),
                "date" => $getConversation->getDate()
            ];
        }
        return $this->json($tab);
    }

    // Fonction de suppression d'un seul message
    #[Route('/deleteMessage/{id}', name: 'app_deleteMessage')]
    public function deleteMessage(ManagerRegistry $doctrine, int $id)
    {
        $entityManager = $doctrine->getManager();

        $message = $doctrine->getRepository(Message::class)->find($id);

        $entityManager->remove($message);
        $entityManager->flush();

        return $this->redirect('../api/showMessage');
    }
}
