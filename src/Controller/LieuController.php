<?php
namespace App\Controller;

use App\Controller\CommonController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Lieu;
use App\Form\LieuType;

/**
 * @Route("/website/lieu")
 */
class LieuController extends CommonController
{
    /**
     * @Route("/ajouter", name="lieu_ajouter")
     */
    public function ajouterLieu(EntityManagerInterface $em, Request $request) {
        $lieu = new Lieu();
        dump($request->query->all());
        $lieuForm = $this->createForm(LieuType::class, $lieu, []);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('sortie_ajouter',['lieu'=>$lieu->getId(), 'params' => $request->query->all()]);
        } else {
            return $this->render('lieu/ajouter_lieu.html.twig', [
                'page_name' => 'Création d\'un lieu',
                'lieu_form' => $lieuForm->createView(),
                'title' => 'Ajout de lieu',
                // 'routes' => $this->getAllRoutes()
            ]);
        }
        
        
    }
    
}

