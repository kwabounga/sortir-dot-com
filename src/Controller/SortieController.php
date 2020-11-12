<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\SortieAnnulees;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        dump($sortie);
        return $this->render('sortie/detail_sortie.html.twig', [
            'sortie' => $sortie,
            // 'routes' => $this->getAllRoutes(),
            'title' => $sortie->getNom(),
        ]);
    }

    /**
     * @Route("/ajouter/{lieu}", name="sortie_ajouter")
     */
    public function ajouterSortie(EntityManagerInterface $em, Request $request, $lieu = null) {
        $sortie = new Sortie();
        
        $p = $request->query->all();
        
        $date = new \DateTime();
        $date->modify('+1 day');
        
        if (!empty($p)) {
            $sortie->setNom($p['params']['nom']);
            $sortie->setDebut(new \DateTime($p['params']['debut']['date']));
            $sortie->setDuree(new \DateTime($p['params']['duree']['date']));
            $sortie->setLimiteInscription(new \DateTime($p['params']['limiteInscription']['date']));
            $sortie->setInscriptionMax($p['params']['inscriptionMax']);
            $sortie->setInfos($p['params']['infos']);
        } else {
            $sortie->setDebut($date);
            $sortie->setLimiteInscription(new \DateTime());
        }
        
        if($lieu){
            $sortie->setLieu($em->getRepository(Lieu::class)->find($lieu));
        }
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
                // 'routes' => $this->getAllRoutes()
            ]);
        }
        
        
    }

    /**
     * @Route("/modifier/{id}", name="sortie_modifier", requirements={"id": "\d+"})
     */
    public function modifierSortie(EntityManagerInterface $em, $id, Request $request, $lieu = null) {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        
        $p = $request->query->all();
        
        if (!empty($p)) {
            $sortie->setNom($p['params']['nom']);
            $sortie->setDebut(new \DateTime($p['params']['debut']['date']));
            $sortie->setDuree(new \DateTime($p['params']['duree']['date']));
            $sortie->setLimiteInscription(new \DateTime($p['params']['limiteInscription']['date']));
            $sortie->setInscriptionMax($p['params']['inscriptionMax']);
            $sortie->setInfos($p['params']['infos']);
        } else {
            $sortie->setDebut(new \DateTime());
            $sortie->setLimiteInscription(new \DateTime());
        }
        
        if($lieu){
            $sortie->setLieu($em->getRepository(Lieu::class)->find($lieu));
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $this->getUser(),
        ]);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setCampus($this->getUser()->getCampus());
            
            if ($sortieForm->get('save')->isClicked()) {
                $em->persist($sortie);
                $em->flush();
                return $this->redirectToRoute('sortie_modifier', ['id' => $sortie->getId()]);
            } elseif ($sortieForm->get('publish')->isClicked()) {
                $sortie->setEtat($this->getDoctrine()->getRepository(Etat::class)->find(2));
                $em->persist($sortie);
                $em->flush();
                return $this->redirectToRoute('home');
            } else {
                return $this->redirectToRoute('home');
            }
           
            
        } else {
            return $this->render('sortie/modifier_sortie.html.twig', [
                'page_name' => 'Modification d\'une sortie',
                'sortie_form' => $sortieForm->createView(),
                'user' => $this->getUser(),
                'title' => 'Modification de Sortie',
                'sortie' => $sortie,
            ]);
        }
    }

    /**
     * @Route("/remettre/{id}", name="sortie_remettre", requirements={"id": "\d+"})
     */
    public function remettreSortie(EntityManagerInterface $em, Request $request,SortieRepository $sortieRepo, $id) {
        $sortie = $sortieRepo->find($id);
        $sa = $sortie->getAnnulation();
        $sortie->setAnnulation(null);
        $sortie->setEtat($em->getRepository(Etat::class)->find(1));
        $em->remove($sa);
        $em->flush();
        return $this->redirectToRoute('sortie_detail',['id'=>$id]);
    }

    /**
     * @Route("/annuler/{id}", name="sortie_annuler", requirements={"id": "\d+"})
     */
    public function annulerSortie(EntityManagerInterface $em, Request $request,SortieRepository $sortieRepo, $id) {
        $sortie = $sortieRepo->find($id);
        $params = $request->request->all();

        if($params != []){
            if($params['id'] == $id){
                $sa = new SortieAnnulees();
                $sa->setMotif($params['motif']);
                $sortie->setAnnulation($sa);
                $sortie->setEtat($em->getRepository(Etat::class)->find(5));
                $em->persist($sortie);
                $em->persist($sa);
                $em->flush();
                return new JsonResponse('ok');
            }

        }

        return $this->render('sortie/annuler_sortie.html.twig', [
            'sortie' => $sortie,
            // 'routes' => $this->getAllRoutes(),
            'title' => 'Sortie '. $sortie->getNom(),
        ]);
    }
}