<?php

namespace App\Repository;

use App\Entity\PlayerRound;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerRound>
 *
 * @method PlayerRound|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerRound|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerRound[]    findAll()
 * @method PlayerRound[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRoundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerRound::class);
    }

    //    /**
    //     * @return PlayerRound[] Returns an array of PlayerRound objects
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

    //    public function findOneBySomeField($value): ?PlayerRound
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
