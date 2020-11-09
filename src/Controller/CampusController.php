<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/website/campus")
 */
class CampusController extends CommonController {

    /**
     * @Route("/", name="campus_liste")
     */
    public function listeCampus(CampusRepository $campusRepo, EntityManagerInterface $em, Request $request) {
        $listeCampus = $campusRepo->findAll();

        // TODO - ajouter: Formulaire nouveau campus

        return $this->render('campus/liste_campus.html.twig', [

            // 'routes' => $this->getAllRoutes(),
            'title' => 'Campus',
        ]);
    }
}