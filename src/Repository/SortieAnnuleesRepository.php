<?php

namespace App\Repository;

use App\Entity\SortieAnnulees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SortieAnnulees|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortieAnnulees|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortieAnnulees[]    findAll()
 * @method SortieAnnulees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieAnnuleesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortieAnnulees::class);
    }

    // /**
    //  * @return SortieAnnulees[] Returns an array of SortieAnnulees objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SortieAnnulees
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
