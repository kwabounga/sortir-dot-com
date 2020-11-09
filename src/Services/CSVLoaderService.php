<?php


namespace App\Services;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CSVLoaderService

{
    /* chargement de villes a la volée avec csv */
    public static function loadCitiesFromCSV(EntityManagerInterface $em, $csv) {
        $output="";
        $row = 1;
        $nbCol = 4;
        $currentCampus = 1;
        $villes = [];
        $villeNames = [];
        $i = 0 ;
        $c=0;
        $data = str_getcsv($csv, ";", "'");
        //echo $data;
        $v = new Ville();
        // 'CHAUMES EN RETZ',44320,47.1592162214,-1.95412512421
        while (($data[$i])) {
            $num = count($data);
            $d = $data[$i];
            switch ($c){
                case 0:
                    // Nom
                    $v->setNom($d);
                    break;
                case 1:
                    //cp
                    $v->setCodePostal($d);
                    break;
                case 2:
                    // latitude
                    $v->setLatitude($d);
                    break;
                case 3:
                    // longitude
                    $v->setLongitude($d);
                    break;
}
            $i++;
            $c++;
            if($c >= $nbCol){
                $c = 0;
            }
            if(($i) % $nbCol ==0){
                $em->persist($v);
                try {
                    $em->flush();
                    $villeNames[] = [$v->getNom() => 'added'];
                    // $output = $output.' import' . $v->getNom() . "OK\n";

                } catch (\Exception $e){
                    dump($e);
//                    $output = $output.'import'.$v->getNom().'FAIL'. $e->getMessage();
                    $villeNames[] = [$v->getNom() => 'error'];
                }
                array_push($villes, $v);
                $row++;
                $v = new Ville();
            }
        }
        dump($villes);
//        try {
//            $em->flush();
//            $output = 'import villes ok';
//        } catch (\Exception $e){
//            dump($e);
//            $output = 'import fail '. $e->getMessage();
//        }
        return new JsonResponse($villeNames);
    }
    /* chargement d'utilisateur à la volée via csv */
    public static function loadUsersFromCSV(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $csv) {
        $output="";
        $row = 1;
        $nbCol = 8;
        $users = [];
        $i = 0 ;
        $c=0;
        $data = str_getcsv($csv, ";", "'");
        //echo $data;
        $u = new User();
        while (($data[$i])) {
            $num = count($data);
             $d = $data[$i];
            switch ($c){
                case 0:
                    // campus
                    $u->setCampus($em->getRepository(Campus::class)->find($d));
                    break;
                case 1:
                    //role
                    $u->setRole($em->getRepository(Role::class)->find($d));
                    break;
                case 2:
                    // username
                    $u->setUsername($d);
                    break;
                case 3:
                    // mail
                    $u->setMail($d);
                    break;
                case 4:
                    // password
                    $hash = $encoder->encodePassword($u, $d);
                    $u->setPassword($hash);
                    break;
                case 5:
                    // firstname
                    $u->setFirstname($d);
                    break;
                case 6:
                    // lastname
                    $u->setLastname($d);
                    break;
                case 7:
                    // phone
                    $u->setPhone($d);
                    break;
            }
            $i++;
            $c++;
            if($c >= $nbCol){
                $c = 0;
            }
            if(($i) % $nbCol ==0){
                $u->setDateCreated(new \DateTime());
                $u->setActif(true);

                //array_push($users, $u);
                try {
                    $em->persist($u);
                    $em->flush();
                    $users[] = [$u->getUsername() =>'added'];
                } catch (\Exception $e){
                    dump($e);
                    $users[] = [$u->getUsername() => 'error'];
                }
                $row++;
                $u = new User();
            }
        }
        //dump($users);

        return new JsonResponse($users);
    }


}