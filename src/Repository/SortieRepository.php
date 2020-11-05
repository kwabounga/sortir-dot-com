<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\Model\FiltreHomeDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortieFiltre(FiltreHomeDTO $filtre, int $idUser) {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.campus = :campusId')
            ->setParameter('campusId', $filtre->campusSearch->getId());

        if (isset($filtre->dateDebutSearch)) {
            $qb->andWhere(':dateDebut <= s.debut')
                ->setParameter('dateDebut', $filtre->dateDebutSearch->format('Y-m-d 00:00:00'));
        }

        if (isset($filtre->dateFinSearch)) {
            $qb->andWhere(':dateFin >= s.debut')
                ->setParameter('dateFin', $filtre->dateFinSearch->format('Y-m-d 23:59:59'));
        }

        if ($filtre->sortieOrgaSearch) {
            $qb->andWhere('s.organisateur = :idOrga')
                ->setParameter('idOrga', $idUser);
        }

        if ($filtre->sortieInscritSearch && !$filtre->sortiePasInscritSearch) {
            var_dump('test sortieInscritSearch');
            $qb->innerJoin('s.participants', 'p')
                ->andWhere('p.id = :idIns')
                ->setParameter('idIns', $idUser);
        }

        if ($filtre->sortiePasInscritSearch && !$filtre->sortieInscritSearch) {
            var_dump('test sortiePasInscritSearch');
            $subQuery = $this->_em->createQueryBuilder()
                ->select('u.id')->from(User::class, 'u')
                ->innerJoin('u.sorties', 'su')
                ->andWhere('s.id = su.id');

            $qb->andWhere($qb->expr()->notIn(':idPasIns', $subQuery->getDQL()))
                ->setParameter('idPasIns', $idUser);
        }

        if ($filtre->sortiePasseeSearch) {
            $qb->andWhere(':dateNow >= s.debut')
                ->setParameter('dateNow', date('Y-m-d 00:00:00'));
        } else {
            $qb->andWhere(':dateNow <= s.debut')
                ->setParameter('dateNow', date('Y-m-d 23:59:59'));
        }

        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
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
    public function findOneBySomeField($value): ?Sortie
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
