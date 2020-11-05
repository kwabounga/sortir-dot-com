<?php

namespace App\Controller;

use App\Entity\Sortie;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Form\FiltreHomeType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use DateTime;
use FiltreHomeDTO;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController {

    private $listeSorties;
    private $filtre;

    /**
     * @Route("/", name="home")
     */
    public function main() {
        return $this->redirectToRoute('main_home');
    }

    /**
     * @Route("/website", name="main_home")
     */
    public function home(SortieRepository $sortieRepo, Request $request) {
        $user = $this->getUser();
        $filtre = new FiltreHomeDTO($user);

        $filtreForm = $this->createForm(FiltreHomeType::class, $filtre);
        $filtreForm->handleRequest($request);

        if ($filtreForm->isSubmitted() && $filtreForm->isValid()) {
            $filtre = $filtreForm->getData();
        }

        $listeSorties = $sortieRepo->findSortieFiltre($filtre, $user->getId());

        return $this->render('main/home.html.twig', ['filtreForm' => $filtreForm->createView(), 'listeSorties' => $listeSorties]);
    }
}