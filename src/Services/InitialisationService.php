<?php


namespace App\Services;


use App\Entity\Etat;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class InitialisationService
{
    public static function firstInitBdd(EntityManagerInterface $em) {
        InitialisationService::initRoles($em);
        InitialisationService::initCampus($em);
        InitialisationService::initVilles($em);
        InitialisationService::initEtats($em);
    }
    private static function initRoles(EntityManagerInterface $em) {
        $roles = $em->getRepository(Role::class)->findAll();
        if(count($roles) === 0 ){
            $r1 = new Role();
            $r1->setValue('ROLE_ADMIN');
            $r2 = new Role();
            $r2->setValue('ROLE_USER');
            $em->persist($r1);
            $em->persist($r2);
            $em->flush();
        } else {
            // roles deja initialisés
        }
    }
    private static function initCampus(EntityManagerInterface $em) {  }
    private static function initVilles(EntityManagerInterface $em) {  }
    private static function initEtats(EntityManagerInterface $em) {
        $states = $em->getRepository(Etat::class)->findAll();
        if(count($states) === 0 ){
            $as = ['en creation', 'ouverte', 'clôturée', 'en cours', 'terminée', 'historisée'];
            for ($i = 0 ; $i<count($as); $i++){
                $r = new Etat();
                $r->setLibelle($as[$i]);
                $em->persist($r);
            }
            $em->flush();
        } else {
            // etats deja initialisés
        }
    }
}