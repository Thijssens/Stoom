<?php

namespace App\Repository;

use App\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Score>
 */
class ScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Score::class);
    }

    public function findLowestTimeByUserId($value): ?array
    {
        return $this->createQueryBuilder('s')
            ->select('MIN(s.time)')
            ->andWhere('s.user = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findHighestScoreByUserId($value): ?array
    {
        return $this->createQueryBuilder('s')
            ->select('MAX(s.score)')
            ->andWhere('s.user = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countPlayedGamesByUserId($value): ?array
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(DISTINCT s.game)')  //pakt unieke velden en telt ze
            ->andWhere('s.user = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Score[] Returns an array of Score objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Score
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
