<?php


namespace App\Services;


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
    const DEFAULTERROR = 'Une erreur est survenue';

    /* messages inscription */
    const INSCRIPTION = 'Inscription réussi';
    const DEINSCRIPTION = 'Déinscription réussi';

    /* Gestion sortie */
    const PUBLICATION = 'Publication réussi';
    const ANNULATION = 'Annulation réussi';
    const RAZMDPSENDED = 'Une demande de confirmation vient de vous être envoyé: lien valable ce jour';
    const USERNOEXIST = 'L\'Utilisateur n\'existe pas';
    const RAZMDPOK = 'la reinitialisation du mot de passe c\'est déroulé avec succes';
    const USERDESACTIVATED = 'Votre Compte a été désactivé contactez votre administrateur';


}