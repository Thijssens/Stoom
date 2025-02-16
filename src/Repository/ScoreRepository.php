<?php

namespace App\Repository;

use App\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
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

    public function findPlayedGamesByUserId($value): array
    {
        return $this->createQueryBuilder('s')
            ->select('DISTINCT g.name, g.id')
            ->innerJoin('s.game', 'g') // Verwijzing naar de relatie in je entity
            ->andWhere('s.user = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }


    public function countGamePlayedByUserIdAndGameId($userId, $gameId): array
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT (s.id)')
            ->andWhere('s.user = :userId AND s.game = :gameId')
            ->setParameter('userId', $userId)
            ->setParameter('gameId', $gameId)
            ->getQuery()
            ->getResult()
        ;
    }



    //om leaderboard geordend te orderenen
    public function findPlayedGamesByGameIdOrderBy($GameId, $orderby, $direction): array
    {
        return $this->createQueryBuilder('s')
            ->select('u.username, s.score, s.time')
            ->innerJoin('s.user', 'u') // Verwijzing naar de relatie in je entity
            ->andWhere('s.game = :gameId')
            ->setParameter('gameId', $GameId)
            ->orderBy($orderby, $direction)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
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
