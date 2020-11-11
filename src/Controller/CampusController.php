<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'listeCampus' => $listeCampus,
        ]);
    }
    /**
     * @Route("/modify", name="campus_modify",methods={"POST"})
     */
    public function modifyCampus(CampusRepository $campusRepo, EntityManagerInterface $em, Request $request) {


        $infosCampus = $request->query->all();
        dump($infosCampus);
        if($infosCampus['id'] != '' && $infosCampus['campus'] != '' ){
            $c = $em->getRepository(Campus::class)->find($infosCampus['id']);
            if($c){
                $c->setNom($infosCampus['campus']);
                try {
                    $em->persist($c);
                    $em->flush();

                } catch (\Exception $e) {
                    return new JsonResponse(['action' => 'error something wrong appenned']);
                }
                return new JsonResponse(['action' => 'success campus modified ']);
            } else {
                return new JsonResponse(['action' => 'error campus no exist']);
            }
        }else {
            return new JsonResponse(['action' => 'campus value must not be empty']);
        }



    }
    /**
     * @Route("/add", name="campus_add",methods={"POST"})
     */
    public function addCampus(CampusRepository $campusRepo, EntityManagerInterface $em, Request $request) {
        $infosCampus = $request->query->all();
        dump($infosCampus);
        if($infosCampus['campus'] != '' ){
            if($infosCampus['action'] == 'add'){
                $c = new Campus();
                $c->setNom($infosCampus['campus']);
                try {
                    $em->persist($c);
                    $em->flush();

                } catch (\Exception $e) {
                    return new JsonResponse(['action' => 'error something wrong appenned']);
                }
                return new JsonResponse(['action' => 'success campus added']);
            } else {
                return new JsonResponse(['action' => 'error bad format']);
            }
        }else {
            return new JsonResponse(['action' => 'campus value must not be empty']);
        }
    }
    /**
     * @Route("/delete", name="campus_delete",methods={"POST"})
     */
    public function deleteCampus(CampusRepository $campusRepo, EntityManagerInterface $em, Request $request) {


        $infosCampus = $request->query->all();
        dump($infosCampus);
        if($infosCampus['id'] != '' ){
            if($infosCampus['action'] == 'delete'){
                $c = $em->getRepository(Campus::class)->find($infosCampus['id']);
                try {
                    $em->remove($c);
                    $em->flush();

                } catch (\Exception $e) {
                    return new JsonResponse(['action' => 'error something wrong appenned']);
                }
                return new JsonResponse(['action' => 'success campus suppressed']);
            } else {
                return new JsonResponse(['action' => 'error bad format']);
            }
        }else {
            return new JsonResponse(['action' => 'id value must not be empty']);
        }



    }
}