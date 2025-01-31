<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(value="today", message="Le début de la soirée ne peut pas être dans le passé.")
     */
    private $debut;

    /**
     * @ORM\Column(type="time")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThan(propertyPath="debut", message="La limite d'incription doit se situer avant le début.")
     * @Assert\GreaterThan(value="today", message="La limite d'inscription ne peut pas être dans le passé.")
     */
    private $limiteInscription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(value=1, message="Une soirée ne peut pas s'effectuer tout seul.")
     */
    private $inscriptionMax;

    /**
     * @ORM\Column(type="text")
     */
    private $infos;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
      */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="sorties")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sortiesOrganisees")
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
    */
    private $lieu;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SortieAnnulees")
     */
    private $annulation;

    /**
     * @return mixed
     */
    public function getAnnulation()
    {
        return $this->annulation;
    }

    /**
     * @param mixed $annulation
     */
    public function setAnnulation($annulation): void
    {
        $this->annulation = $annulation;
    }


    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): self {
        $this->duree = $duree;
    
        return $this;
    }

    public function getLimiteInscription(): ?\DateTimeInterface
    {
        return $this->limiteInscription;
    }

    public function setLimiteInscription(\DateTimeInterface $limiteInscription): self
    {
        $this->limiteInscription = $limiteInscription;

        return $this;
    }

    public function getInscriptionMax(): ?int
    {
        return $this->inscriptionMax;
    }

    public function setInscriptionMax(int $inscriptionMax): self
    {
        $this->inscriptionMax = $inscriptionMax;

        return $this;
    }

    public function getInfos(): ?string
    {
        return $this->infos;
    }

    public function setInfos(string $infos): self
    {
        $this->infos = $infos;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }
}
