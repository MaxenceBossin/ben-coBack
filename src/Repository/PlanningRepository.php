<?php

namespace App\Repository;

use App\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Immutable;

/**
 * @extends ServiceEntityRepository<Planning>
 *
 * @method Planning|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planning|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planning[]    findAll()
 * @method Planning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    public function save(Planning $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Planning $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function fetchWithDate($date)
    {
        $conn = $this->getEntityManager()->getConnection();
        $dateEnd = $date->modify('+5 day');
        $sql = '
            SELECT * FROM planning p
            WHERE p.date BETWEEN :date AND :dateEnd
            ORDER BY p.date ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['date' => $date->format('Y-m-d'), 'dateEnd' => $dateEnd->format('Y-m-d')]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    public function fetchWith1Date($date)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT * FROM planning p
            WHERE p.date = :date
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['date' => $date->format('Y-m-d')]);

        return $resultSet->fetchAllAssociative();
    }
    public function replace($date,$team)
    {
        // $team = '["testtest","test"]';
        $team = json_encode($team);
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            UPDATE  `planning` SET `team` = :team
            WHERE date = :date
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['date' => $date->format('Y-m-d'),'team' => $team]);

        return $resultSet->fetchAllAssociative();
    }
    //    /**
    //     * @return Planning[] Returns an array of Planning objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Planning
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // ORDER BY p.price ASC
}
