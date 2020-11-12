<?php

namespace App\Controller\Rest;

use App\Controller\CommonController;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\Msgr;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/website/sortieApi")
 */
class SortieRestController extends CommonController {


    /**
     * @Route("/publier", name="sortie_api_publier", methods={"PUT"})
     */
    public function publier(SortieRepository $sortieRepo, EtatRepository $etatRepo, Request $request, EntityManagerInterface  $em) {
        try {
            $idSortie = $request->get('id');
            $sortie = $sortieRepo->find($idSortie);
            $dateActuel = new DateTime();

            if ($sortie->getEtat()->getId() != 1) {
                $this->addFlash(Msgr::TYPE_ERROR, 'La sortie à déjà été publier');

            // Si la date actuel est supérieur ou égal à la date de la sortie
            } elseif ($sortie->getDebut() >= $dateActuel) {
                $this->addFlash(Msgr::TYPE_ERROR, 'La date de la sortie est déjà passé');

            // Si la date actuel est supérieur ou égal à la date d'inscription
            } elseif ($sortie->getLimiteInscription() >= $dateActuel) {
                $this->addFlash(Msgr::TYPE_ERROR, 'La date limite d\'inscription est déjà passé');

            // Si tout est bon
            } else {
                $etat = $etatRepo->find(2);
                $sortie->setEtat($etat);

                $em->persist($sortie);
                $em->flush();

                $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::PUBLICATION);
            }
        } catch (Exception $e) {
            $this->addFlash(Msgr::TYPE_ERROR, Msgr::DEFAULTERROR);
        }

        return $this->json([]);
    }

    /**
     * @Route("/annuler", name="sortie_api_annuler", methods={"PUT"})
     */
    public function annuler(SortieRepository $sortieRepo, EtatRepository $etatRepo, Request $request, EntityManagerInterface  $em) {
        try {
            $idSortie = $request->get('id');
            $sortie = $sortieRepo->find($idSortie);

            // Si l'état de la sortie est 'ouverte' ou 'clôturée'
            if (in_array($sortie->getEtat()->getId(), array(2, 3))) {
                $etat = $etatRepo->find(5);
                $sortie->setEtat($etat);
                
                $em->persist($sortie);
                $em->flush();

                $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::ANNULATION);

            // Si tout est bon
            } else {
                $this->addFlash(Msgr::TYPE_ERROR, 'La sortie ne peut pas être annulée');
            }
        } catch (Exception $e) {
            $this->addFlash(Msgr::TYPE_ERROR, Msgr::DEFAULTERROR);
        }

        return $this->json([]);
    }

    /**
     * @Route("/inscription", name="sortie_api_inscription", methods={"POST"})
     */
    public function inscription(SortieRepository $sortieRepo, EtatRepository $etatRepo, Request $request, EntityManagerInterface  $em) {
        try {
            $idSortie = $request->get('id');
            $sortie = $sortieRepo->find($idSortie);

            // Si l'état est différent de 'ouverte'
            if ($sortie->getEtat()->getId() != 2) {
                $this->addFlash(Msgr::TYPE_ERROR, 'La sortie n\'est pas ouverte');

            // Si l'utilisateur est déjà inscrit
            } elseif ($sortie->getParticipants()->contains($this->getUser())) {
                $this->addFlash(Msgr::TYPE_ERROR, 'Vous êtes déjà inscrit à cette sortie');
            
            // Si tout est bon
            } else {
                $sortieRepo->inscriptionSortie($idSortie, $this->getUser()->getId());

                $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::INSCRIPTION);
            }
            
            // Si la sortie à atteint le nombre maximum de participants
            if ($sortie->getParticipants()->count() +1 == $sortie->getInscriptionMax()) {
                
                $etat = $etatRepo->find(3);
                $sortie->setEtat($etat);

                $em->persist($sortie);
                $em->flush();
            }
        } catch (Exception $e) {
            $this->addFlash(Msgr::TYPE_ERROR, Msgr::DEFAULTERROR);
        }

        return $this->json([]);
    }

    /**
     * @Route("/deinscription", name="sortie_api_deinscription", methods={"DELETE"})
     */
    public function deinscription(SortieRepository $sortieRepo, EtatRepository $etatRepo, Request $request, EntityManagerInterface  $em) {
        try {
            $idSortie = $request->get('id');
            $sortie = $sortieRepo->find($idSortie);
            $dateActuel = new DateTime();

            // Si l'état de la sortie est 'ouverte' ou 'clôturée'
            if (in_array($sortie->getEtat()->getId(), array(2, 3))) {
                $sortieRepo->deInscriptionSortie($idSortie, $this->getUser()->getId());

                $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::DEINSCRIPTION);
            }

            // Si la sortie est clôturée et que la date limite de clôturation n'est pas passée
            if ($sortie->getEtat()->getId() == 3 && $sortie->getLimiteInscription() >= $dateActuel) {
                $etat = $etatRepo->find(2);
                $sortie->setEtat($etat);

                $em->persist($sortie);
                $em->flush();
            }
            
        } catch (Exception $e) {
            $this->addFlash(Msgr::TYPE_ERROR, Msgr::DEFAULTERROR);
        }

        return $this->json(['idSortie' => $idSortie]);
    }
}