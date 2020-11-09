<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use App\Services\CSVLoaderService;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
USE Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @Route("/website/ville")
 */
class VilleController extends CommonController {

    /**
     * @Route("/", name="ville_liste")
     */
    public function listeVille(EntityManagerInterface $em, Request $request) {
        //mode Création
        $v = new Ville();
        $villeForm = $this->createForm(VilleType::class,$v);
        $villeForm->handleRequest($request);

        // tentative de recuperation de l'existant si existant
        $vv = $em->getRepository(Ville::class)->findOneBy(['nom' => $v->getNom()]);
        if($vv){
            // passage en mode Mode modification
            $v = $vv;
            $villeForm = $this->createForm(VilleType::class,$v);
            $villeForm->handleRequest($request);
        }

        //dump($villeForm);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            try {
                $em->persist($v);
                $em->flush();
                if($vv){
                    $this->addFlash(Msgr::TYPE_SUCCESS, 'la ville '.$v->getNom().' a bien été Modifié');
                }else {
                    $this->addFlash(Msgr::TYPE_SUCCESS, 'la ville '.$v->getNom().' a bien été ajouté en base');
                }

            } catch (\Exception $e){
                dump($e);
                $this->addFlash(Msgr::TYPE_WARNING, 'la ville '.$v->getNom().' n\'a pas pu etre ajouté');
            }
            // vidage &  re-création d'un form tou neuf
            $villeForm = $this->createForm(VilleType::class);
        }
        $cities = $em->getRepository(Ville::class)->findAll();
        return $this->render('ville/liste_ville.html.twig',[

            'title' => 'Villes',
            'villes' => $cities,
            'ville_form' => $villeForm->createView(),
            'user'=> $this->getUser()
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
        /** @var JsonResponse $output */
        $output = CSVLoaderService::loadCitiesFromCSV($em, $csv);
        return $output;
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