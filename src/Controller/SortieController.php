<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\SortieType;

/**
 * @Route("/website/sortie")
 */
class SortieController extends CommonController {

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
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $this->getUser(),
        ]);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setCampus($this->getUser()->getCampus());
            
            if ($sortieForm->get('save')->isClicked()) {
                $sortie->setEtat($this->getDoctrine()->getRepository(Etat::class)->find(1));
                $em->persist($sortie);
                $em->flush();
            }
            if ($sortieForm->get('publish')->isClicked()) {
                $sortie->setEtat($this->getDoctrine()->getRepository(Etat::class)->find(2));
                $sortie->getParticipants()->add($this->getUser());
                $em->persist($sortie);
                $em->flush();
            }
            return $this->redirectToRoute('home');
                    
        } else {
            return $this->render('sortie/ajouter_sortie.html.twig', [
                'page_name' => 'Création d\'une sortie',
                'sortie_form' => $sortieForm->createView(),
                'user' => $this->getUser(),
                'title' => 'Ajout de Sortie',
                'routes' => $this->getAllRoutes()
            ]);
        }
        
        
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