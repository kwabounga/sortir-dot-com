<?php


namespace App\Services;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Msgr

{
    /* types de message */
    // utilisés pour la coloration des messages flash via le css des toasts
    const TYPE_INFOS = 'infos';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    /* messages flash récurents */
    const IMPOSSIBLE = 'operation impossible';
    const FIRST_CONNEXION = 'premiere connection : configuration du compte administrateur';
    const MUST_BE_ADMIN = 'vous devez etre admin pour venir ici';
    const WELCOME = 'Ravi de vous revoir sur notre site ';


}