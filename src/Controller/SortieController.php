<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/website/sortie")
 */
class SortieController extends AbstractController {

    /**
     * @Route("/{id}", name="sortie_detail", requirements={"id": "\d+"})
     */
    public function detailSortie(SortieRepository $sortieRepo, $id) {
        $sortie = $sortieRepo->find($id);

        return $this->render('sortie/detail_sortie.html.twig', compact("sortie"));
    }

    /**
     * @Route("/ajouter", name="sortie_ajouter")
     */
    public function ajouterSortie(EntityManagerInterface $em, Request $request) {
        $sortie = new Sortie();

        // // TODO - ajouter: Formulaire nouvelle sortie

        return $this->render('sortie/ajouter_sortie.html.twig');
    }

    /**
     * @Route("/modifier/{id}", name="sortie_modifier", requirements={"id": "\d+"})
     */
    public function modifierSortie(SortieRepository $sortieRepo, $id) {
        $sortie = $sortieRepo->find($id);

        return $this->render('sortie/modifier_sortie.html.twig', compact("sortie"));
    }

    /**
     * @Route("/annuler/{id}", name="sortie_annuler", requirements={"id": "\d+"})
     */
    public function annulerSortie(SortieRepository $sortieRepo, $id) {
        $sortie = $sortieRepo->find($id);

        return $this->render('sortie/annuler_sortie.html.twig', compact("sortie"));
    }
}