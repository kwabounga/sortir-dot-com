<?php
namespace App\Controller;

use App\Controller\CommonController;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;

/**
 * @Route("/website/lieu")
 */
class LieuController extends CommonController
{
    /**
     * @Route("/ajouter/{ville}", name="lieu_ajouter")
     */
    public function ajouterLieu(EntityManagerInterface $em, Request $request, $ville = null) {
        $lieu = new Lieu();
        $params = $request->query->all();

        // dump($request->request->all());
        // dump($request->getRequestUri());

        if (array_key_exists('lieu_nom', $params)) {
            if ($params['lieu_lat'] == '') {
                $params['lieu_lat'] = null;
            }
            if ($params['lieu_lon'] == '') {
                $params['lieu_lon'] = null;
            }
            $lieu->setNom( $params['lieu_nom']);
            $lieu->setRue( $params['lieu_rue']);
            $lieu->setLatitude($params['lieu_lat']);
            $lieu->setLongitude($params['lieu_lon']);
        }
            
        if ($ville) {
            $lieu->setVille($em->getRepository(Ville::class)->find($ville));
        }

        $lieuForm = $this->createForm(LieuType::class, $lieu, []);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();
            $params['lieu'] = $lieu->getId();
            if($params['idSortie'] != 'null'){
                $s = $em->getRepository(Sortie::class)->find($params['idSortie'] );
                if($s){
                    $s->setLieu($lieu);
                    $em->persist($s);
                    $em->flush();
                }
                return $this->redirectToRoute('sortie_modifier', ['id'=>$params['idSortie']]);
            } else {
                return $this->redirectToRoute('sortie_ajouter', $params);

            }
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
        // dump($request->query->all());
        // dump($request->request->all());
        $l = $em->getRepository(Lieu::class)->find($request->request->all()['id']);
        if($l){
            return new JsonResponse(['lat'=>$l->getLatitude(), 'lng'=>$l->getLongitude()]);
        }
        // pour prévenir d'un eventuel bug coordonnées par defaut
            return new JsonResponse(['lat'=>42.01212, 'lng'=>-1.1234]);

    }
    
}

