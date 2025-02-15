<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function findFriendsByUserId($value): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.userId = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findFriendsIdByUserId($value): array
    {
        $results = $this->createQueryBuilder('f')
            ->select("f.friendId")
            ->andWhere('f.userId = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();

        // Zet de id's in 1 array, anders array in array in array
        return array_column($results, 'friendId');
    }


    //    /**
    //     * @return Friendship[] Returns an array of Friendship objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Friendship
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
