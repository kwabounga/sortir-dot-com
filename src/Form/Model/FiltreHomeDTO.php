<?php
namespace App\Form\Model;

use App\Entity\Campus;
use DateTime;

class FiltreHomeDTO {
    private $campusSearch;
    private $dateDebutSearch;
    private $dateFinSearch;

    private $sortieOrgaSearch;
    private $sortieInscritSearch;
    private $sortiePasInscritSearch;
    private $sortiePasseeSearch;

    public function __construct(?Campus $campus, ?DateTime $dateDebut = null, ?DateTime $dateFin = null, bool $sortieOrga = false,
        bool $SortieInscrit = false, bool $sortiePasInscrit = false, bool $sortiePassee = false) {
        $this->campusSearch = $campus;
        $this->dateDebutSearch = $dateDebut;
        $this->dateFinSearch = $dateFin;
        $this->sortieOrgaSearch = $sortieOrga;
        $this->sortieInscritSearch = $SortieInscrit;
        $this->sortiePasInscritSearch = $sortiePasInscrit;
        $this->sortiePasseeSearch = $sortiePassee;
    }

    // =============== Campus ==============
    public function getCampusSearch(): ?Campus
    {
        return $this->campusSearch;
    }

    public function setCampusSearch(?Campus $value): void
    {
        $this->campusSearch = $value;
    }

    // =========== Date de début ============
    public function getDateDebutSearch(): ?DateTime
    {
        return $this->dateDebutSearch;
    }

    public function setDateDebutSearch(?DateTime $value): void
    {
        $this->dateDebutSearch = $value;
    }

    // ============ Date de Fin ============
    public function getDateFinSearch(): ?DateTime
    {
        return $this->dateFinSearch;
    }

    public function setDateFinSearch(?DateTime $value): void
    {
        $this->dateFinSearch = $value;
    }

    // ======== Sortie organisation ========
    public function getSortieOrgaSearch(): ?bool
    {
        return $this->sortieOrgaSearch;
    }

    public function setSortieOrgaSearch(bool $value): void
    {
        $this->sortieOrgaSearch = $value;
    }

    // ========== Sortie inscrit ===========
    public function getSortieInscritSearch(): ?bool
    {
        return $this->sortieInscritSearch;
    }

    public function setSortieInscritSearch(bool $value):void
    {
        $this->sortieInscritSearch = $value;
    }

    // ======== Sortie pas inscrit =========
    public function getSortiePasInscritSearch(): ?bool
    {
        return $this->sortiePasInscritSearch;
    }

    public function setSortiePasInscritSearch(bool $value):void
    {
        $this->sortiePasInscritSearch = $value;
    }

    // ========== Sortie passée ============
    public function getSortiePasseeSearch(): ?bool
    {
        return $this->sortiePasseeSearch;
    }

    public function setSortiePasseeSearch(bool $value):void
    {
        $this->sortiePasseeSearch = $value;
    }

}