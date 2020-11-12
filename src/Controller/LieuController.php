<?php
namespace App\Controller;

use App\Controller\CommonController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $params = $request->query->all();
        
        $params['debut'] = date_create_from_format('j/n/Y G:s', $params['debut']);
        $params['duree'] = date_create_from_format('G:s', $params['duree']);
        $params['limiteInscription'] = date_create_from_format('j/n/Y G:s', $params['limiteInscription']);
        
        $lieuForm = $this->createForm(LieuType::class, $lieu, []);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('sortie_ajouter',['lieu'=>$lieu->getId(), 'params' => $params]);
        } else {
            return $this->render('lieu/ajouter_lieu.html.twig', [
                'page_name' => 'Création d\'un lieu',
                'lieu_form' => $lieuForm->createView(),
                'title' => 'Ajout de lieu',
                'params' => $params,
                // 'routes' => $this->getAllRoutes()
            ]);
        }
        
        
    }
    /**
     * @Route("/get/location", name="lieu_location", methods={"POST"})
     */
    public function locationLieu(EntityManagerInterface $em, Request $request) {
        dump($request->query->all());
        dump($request->request->all());
        $l = $em->getRepository(Lieu::class)->find($request->request->all()['id']);
        if($l){
            return new JsonResponse(['lat'=>$l->getLatitude(), 'lng'=>$l->getLongitude()]);
        }
            return new JsonResponse(['lat'=>42.01212, 'lng'=>-1.1234]);


    }
    
}

