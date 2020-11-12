<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RefreshEtatService {

    public static function refreshListSortie($sorties, EtatRepository $etatRepo, EntityManagerInterface $em) {
        foreach ($sorties as $sortie) {
            $sortie = RefreshEtatService::refreshSortie($sortie, $etatRepo, $em);
        }

        return $sorties;
    }


    public static function refreshSortie($sortie, EtatRepository $etatRepo, EntityManagerInterface $em) {
        $dateDebut = clone $sortie->getDebut();
        $cloneDateDebut = clone $sortie->getDebut();
        $dateFin = $cloneDateDebut->modify("+{$sortie->getDuree()->format('h')} hour +{$sortie->getDuree()->format('i')} minutes");
        $dateActuel = new DateTime();

        // Si l'état n'est pas en création et pas archivé
        if (!in_array($sortie->getEtat()->getId(), [1, 7])) {
            // Si l'état n'est pas annulé
            if (!in_array($sortie->getEtat()->getId(), [5, 6])) {
                // Passage à l'état clôturé
                if ($dateActuel >= $sortie->getLimiteInscription() && $dateActuel < $dateDebut) {
                    $etat = $etatRepo->find(3);
                    $sortie->setEtat($etat);

                    $em->persist($sortie);
                    $em->flush();

                // Passage à l'état en cours
                } elseif ($dateDebut <= $dateActuel && $dateFin > $dateActuel) {
                    $etat = $etatRepo->find(4);
                    $sortie->setEtat($etat);
    
                    $em->persist($sortie);
                    $em->flush();
    
                // Passage à l'état terminé
                } elseif ($dateFin <= $dateActuel) {
                    $etat = $etatRepo->find(6);
                    $sortie->setEtat($etat);
    
                    $em->persist($sortie);
                    $em->flush();
                }

            // Passage à l'état Archivé
            } elseif ($dateFin->modify('+1 month') <= $dateActuel) {
                $etat = $etatRepo->find(7);
                $sortie->setEtat($etat);

                $em->persist($sortie);
                $em->flush();
            }
        }

        return $sortie;
    }

}