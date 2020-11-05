<?php
namespace App\Form\Model;

use App\Entity\Campus;
use App\Entity\User;

class FiltreHomeDTO {
    public $campusSearch;
    public $dateDebutSearch;
    public $dateFinSearch;

    public $sortieOrgaSearch;
    public $sortieInscritSearch;
    public $sortiePasInscritSearch;
    public $sortiePasseeSearch;

    public function __construct(User $user) {
        $this->campusSearch = $user->getCampus();
        $this->dateDebutSearch = null;
        $this->dateFinSearch = null;
        $this->sortieOrgaSearch = true;
        $this->sortieInscritSearch = true;
        $this->sortiePasInscritSearch = true;
        $this->sortiePasseeSearch = false;
    }

}