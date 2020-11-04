<?php

namespace App\Controller;

use App\Entity\Role;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController {

    /*
     * Route principale
     */
    /**
     * @Route("/", name="home")
     */
    public function main(EntityManagerInterface $em) {
        // verification de présences de roles pour determiner si c'est la premiere utilisation ou pas
        $roles = $em->getRepository(Role::class)->findAll();
        if(count($roles) === 0 ){
            // si oui initialisation de la bdd
            return $this->redirectToRoute('update_bdd');
        }
        // si non go page d'acceuil
        return $this->redirectToRoute('main_home');
    }


    /*
     * Route secondaire
     */
    /**
     * @Route("/website", name="main_home")
     */
    public function home() {
        /* error examples */
//        $this->addFlash(Msgr::TYPE_INFOS, '$this->addFlash(\'infos\', \'une information\');');
//        $this->addFlash(Msgr::TYPE_SUCCESS, '$this->addFlash(\'success\', \'une reussite\');');
//        $this->addFlash(Msgr::TYPE_WARNING, '$this->addFlash(\'warning\', \'un avertissement\');');
//        $this->addFlash(Msgr::TYPE_ERROR, '$this->addFlash(\'error\', \'une erreure qui reste\');');

        // si authentifié bienvenue
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::WELCOME.$this->getUser()->getUsername());
        }
        return $this->render('main/home.html.twig');
    }
}