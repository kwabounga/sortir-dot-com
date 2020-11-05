<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\FiltreHomeDTO;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Form\FiltreHomeType;
use App\Repository\SortieRepository;
use DateTime;
use App\Entity\Role;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends CommonController {

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
    public function home(SortieRepository $sortieRepo, Request $request,EntityManagerInterface  $em) {
        /* error examples */
        //        $this->addFlash(Msgr::TYPE_INFOS, '$this->addFlash(\'infos\', \'une information\');');
        //        $this->addFlash(Msgr::TYPE_SUCCESS, '$this->addFlash(\'success\', \'une reussite\');');
        //        $this->addFlash(Msgr::TYPE_WARNING, '$this->addFlash(\'warning\', \'un avertissement\');');
        //        $this->addFlash(Msgr::TYPE_ERROR, '$this->addFlash(\'error\', \'une erreure qui reste\');');

        // si authentifié bienvenue
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::WELCOME.$this->getUser()->getUsername());
        }

        $user = $em->getRepository(User::class)->findOneBy(['username'=>$this->getUser()->getUsername()]);
        $filtre = new FiltreHomeDTO($user);

        $filtreForm = $this->createForm(FiltreHomeType::class, $filtre,[
            'user' => $user
        ]);
        $filtreForm->handleRequest($request);

        if ($filtreForm->isSubmitted() && $filtreForm->isValid()) {
            $filtre = $filtreForm->getData();
        }

        $listeSorties = $sortieRepo->findSortieFiltre($filtre, $user->getId());

        return $this->render('main/home.html.twig',
            [
                'filtreForm' => $filtreForm->createView(),
                'listeSorties' => $listeSorties,
                'routes' => $this->getAllRoutes(),
                'title' => 'Home',
            ]);
    }
}