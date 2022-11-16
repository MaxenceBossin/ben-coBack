<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Message[] Returns an array of Message objects
     */
    public function getConversation($senderId, $receiverId)
    {
        $tab1 = $this->createQueryBuilder('m')
            ->where('m.sender = :sender', 'm.receiver = :receiver')
            ->setParameters(array('sender' => $senderId, 'receiver' => $receiverId))
            ->getQuery()
            ->getResult();

        $tab2 = $this->createQueryBuilder('m')
            ->where('m.sender = :sender', 'm.receiver = :receiver')
            ->setParameters(array('sender' => $receiverId, 'receiver' => $senderId))
            ->getQuery()
            ->getResult();

        $tab = array_merge($tab1, $tab2);
        
        return $tab;
    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
