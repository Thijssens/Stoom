<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findMessagesBetweenUsers(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.receiver = :user2) OR (m.sender = :user2 AND m.receiver = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUnreadMessages(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.receiver = :user AND m.isRead = false')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findSentMessages(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findMessageById($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

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
