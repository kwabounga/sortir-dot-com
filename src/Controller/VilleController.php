<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/website/ville")
 */
class VilleController extends CommonController {

    /**
     * @Route("/", name="ville_liste")
     */
    public function listeVille(VilleRepository $villeRepo, EntityManagerInterface $em, Request $request) {
        $listeVille = $villeRepo->findAll();

        // TODO - ajouter: Formulaire nouvelle ville

        return $this->render('ville/liste_ville.html.twig',[

            'routes' => $this->getAllRoutes(),
            'title' => 'Villes',
        ]);
    }


}