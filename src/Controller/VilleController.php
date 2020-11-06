<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use App\Services\CSVLoaderService;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
USE Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @Route("/website/ville")
 */
class VilleController extends CommonController {

    /**
     * @Route("/", name="ville_liste")
     */
    public function listeVille(VilleRepository $villeRepo, EntityManagerInterface $em, Request $request) {
        $v = new Ville();
        $villeForm = $this->createForm(VilleType::class,$v);
        $villeForm->handleRequest($request);
        dump($villeForm);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            try {
                $em->persist($v);
                $em->flush();
                $this->addFlash(Msgr::TYPE_SUCCESS, 'la ville'.$v->getNom().' a bien été ajouté en base');
            } catch (\Exception $e){
                dump($e);
                $this->addFlash(Msgr::TYPE_WARNING, 'la ville'.$v->getNom().' n\'a pas pu etre ajouté');
            }
            // vidage recreation de l'ancien form
            $villeForm = $this->createForm(VilleType::class);
        }
        $listeVille = $villeRepo->findAll();

        $cities = $em->getRepository(Ville::class)->findAll();
        return $this->render('ville/liste_ville.html.twig',[

            'routes' => $this->getAllRoutes(),
            'title' => 'Villes',
            'villes' => $cities,
            'ville_form' => $villeForm->createView()
        ]);
    }
    /**
     * @Route("/uploadCSVCities", name="ville_upload_csv", methods={"POST"})
     */
    public function updloadVilleCSV(EntityManagerInterface $em, Request $request) {
    /** @var UploadedFile $csvFile */
    $csvFile = $request->files->get('upload');
    $csv = '';
    dump($csvFile);
    if($csvFile['file']){
        $csv = $csvFile['file']->openFile('r')->fread($csvFile['file']->getSize());
    }
        dump($csv);
     $output = CSVLoaderService::loadCitiesFromCSV($em, $csv);
        return $this->json($output);
    }
    /**
     * @Route("/delete/{id}", name="ville_delete_one", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function deleteVille(EntityManagerInterface $em, Request $request, $id=null) {
        try {
            $v =$em->getRepository(Ville::class)->find($id);
            $em->remove($v);
            $em->flush();

        }catch (\Exception $e){
            return $this->json(['error'=>'something wrong append on delete city '.$id,'message' => $e->getMessage()]);
        }
        return $this->json(['response'=>'the citie '.$id.' as been deleted']);

    }


}