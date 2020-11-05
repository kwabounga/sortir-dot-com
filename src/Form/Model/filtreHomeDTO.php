<?php

use App\Entity\Campus;
use App\Entity\User;

class FiltreHomeDTO {
    public Campus $campusSearch;
    public $dateDebutSearch;
    public $dateFinSearch;

    public bool $sortieOrgaSearch;
    public bool $sortieInscritSearch;
    public bool $sortiePasInscritSearch;
    public bool $sortiePasseeSearch;

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