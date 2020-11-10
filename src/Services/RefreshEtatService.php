<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RefreshEtatService {
    
    public static function refresh($sorties, EtatRepository $etatRepo, EntityManagerInterface $em) {
        foreach ($sorties as $sortie) {
            $dateDebut = clone $sortie->getDebut();
            $cloneDateDebut = clone $sortie->getDebut();
            $dateFin = $cloneDateDebut->modify("+{$sortie->getDuree()->format('h')} hour +{$sortie->getDuree()->format('i')} minutes");

            if (in_array($sortie->getEtat()->getId(), [2, 3])) {

                if ($dateDebut->format('D, d M Y h:i:s') <= date('D, d M Y h:i:s') && $dateFin->format('D, d M Y h:i:s') > date('D, d M Y h:i:s')) {
                    $etat = $etatRepo->find(4);
                    $sortie->setEtat($etat);

                    $em->persist($sortie);
                    $em->flush();
                }
            } elseif ($sortie->getEtat()->getId() == 4 && $dateFin->format('D, d M Y h:i:s') <= date('D, d M Y h:i:s')) {
                $etat = $etatRepo->find(6);
                $sortie->setEtat($etat);

                $em->persist($sortie);
                $em->flush();
                
            } else if (in_array($sortie->getEtat()->getId(), [5, 6]) && $dateFin->modify('+1 month')->format('D, d M Y h:i:s') <= date('D, d M Y h:i:s')) {
                $etat = $etatRepo->find(7);
                $sortie->setEtat($etat);

                $em->persist($sortie);
                $em->flush();
            }
        }

        return $sorties;
    }
}