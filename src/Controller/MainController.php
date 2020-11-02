<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function main() {
        return $this->redirectToRoute('main_home');
    }

    /**
     * @Route("/website", name="main_home")
     */
    public function home() {
        $user = $this->getUser();
        return $this->render('main/home.html.twig', [
            "user" => $user
        ]);
    }
}