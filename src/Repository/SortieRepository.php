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

    /**
     * Filtre les sorties
     */
    public function findSortieFiltre(FiltreHomeDTO $filtre, int $idUser) {
        $qb = $this->createQueryBuilder('s');

        if ($filtre->getCampusSearch() !== null) {
            $qb->andWhere('s.campus = :campusId')
                ->setParameter('campusId', $filtre->getCampusSearch()->getId());
        }

        if ($filtre->getDateDebutSearch() !== null) {
            $qb->andWhere(':dateDebut <= s.debut')
                ->setParameter('dateDebut', $filtre->getDateDebutSearch()->format('Y-m-d h:i:s'));
        }

        if ($filtre->getDateFinSearch() !== null) {
            $qb->andWhere(':dateFin >= s.debut')
                ->setParameter('dateFin', $filtre->getDateFinSearch()->format('Y-m-d h:i:s'));
        }

        if ($filtre->getSortieOrgaSearch()) {
            $qb->andWhere('s.organisateur = :idOrga')
                ->setParameter('idOrga', $idUser);
        }

        if ($filtre->getSortieInscritSearch() && !$filtre->getSortiePasInscritSearch()) {
            var_dump('test sortieInscritSearch');
            $qb->innerJoin('s.participants', 'p')
                ->andWhere('p.id = :idIns')
                ->setParameter('idIns', $idUser);
        }

        if ($filtre->getSortiePasInscritSearch() && !$filtre->getSortieInscritSearch()) {
            var_dump('test sortiePasInscritSearch');
            $subQuery = $this->_em->createQueryBuilder()
                ->select('u.id')->from(User::class, 'u')
                ->innerJoin('u.sorties', 'su')
                ->andWhere('s.id = su.id');

            $qb->andWhere($qb->expr()->notIn(':idPasIns', $subQuery->getDQL()))
                ->setParameter('idPasIns', $idUser);
        }

        if ($filtre->getSortiePasseeSearch()) {
            $qb->andWhere(':dateNow >= s.debut')
                ->setParameter('dateNow', date('Y-m-d h:i:s'));
        } else {
            $qb->andWhere(':dateNow <= s.debut')
                ->setParameter('dateNow', date('Y-m-d h:i:s'));
        }

        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     * Inscrit un utilisateur à une sortie
     */
    public function inscriptionSortie(int $idSortie, int $idUser): void {
        $sql = 'INSERT INTO sortie_user (sortie_id, user_id) VALUES (:idSortie, :idUser)';

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('idSortie', $idSortie);
        $stmt->bindValue('idUser', $idUser);

        $stmt->execute();
    }

    /**
     * Déinscrit un utilisateur à une sortie
     */
    public function deInscriptionSortie(int $idSortie, int $idUser): void {
        $sql = 'DELETE FROM sortie_user WHERE sortie_id = :idSortie AND user_id = :idUser';

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('idSortie', $idSortie);
        $stmt->bindValue('idUser', $idUser);

        $stmt->execute();
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
