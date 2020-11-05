<?php


namespace App\Services;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CSVLoaderService

{
    /* initialisation de la base de donnée au demarrage */
    public static function load(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $csv) {
        $output="";
        $row = 1;
        $nbCol = 8;
        $currentCampus = 1;
        $users = [];
        $i = 0 ;
        $c=0;
        $data = str_getcsv($csv, ";", "'");
        //echo $data;
        $u = new User();
        while (($data[$i])) {
            $num = count($data);
            //echo "<p> $row $data[$i] $num</p>\n";


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
                $em->persist($u);
                //array_push($users, $u);
                $row++;
                $u = new User();
            }
        }
        //dump($users);
        try {
            $em->flush();
            $output = 'import user ok';
        } catch (\Exception $e){
            dump($e);
            $output = 'import fail '. $e->getMessage();
        }
        return $output;
    }


}