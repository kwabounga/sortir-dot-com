<?php

namespace App\Controller;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function main(EntityManagerInterface $em) {
        $roles = $em->getRepository(Role::class)->findAll();
        if(count($roles) === 0 ){
            return $this->redirectToRoute('update_bdd');
        }
        return $this->redirectToRoute('main_home');
    }

    /**
     * @Route("/website", name="main_home")
     */
    public function home() {
        return $this->render('main/home.html.twig');
    }
}