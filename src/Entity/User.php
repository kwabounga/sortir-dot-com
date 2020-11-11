<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="app_user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3, minMessage="le pseudo doit faire au moins 3 carateres", max=50, maxMessage="le pseudo doit faire 50 carateres maximum")
     * @ORM\Column(type="string", length=50, unique=true);
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3, minMessage="le mail doit faire au moins 8 carateres", max=255, maxMessage="le mail doit faire 255 carateres maximum")
     * @ORM\Column(type="string", length=255, unique=true);
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */

    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;



    /**
     * @ORM\Column(type="string", length=10)
     */
    private $phone;



    /**
     * @ORM\Column(type="datetime");
     */
    private $dateCreated;


    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="etudiants")
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="participants")
     */
    private $sorties;
    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesOrganisees;


    /**
     * @ORM\ManyToOne (targetEntity="App\Entity\Role")
     */
    private $role;

    /**
     * @ORM\Column(type="boolean", options={"default" : TRUE})
     */
    private $actif = TRUE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getSorties()
    {
        return $this->sorties;
    }

    /**
     * @param mixed $sorties
     */
    public function setSorties($sorties): void
    {
        $this->sorties = $sorties;
    }

    /**
     * @return mixed
     */
    public function getSortiesOrganisees()
    {
        return $this->sortiesOrganisees;
    }

    /**
     * @param mixed $sortiesOrganisees
     */
    public function setSortiesOrganisees($sortiesOrganisees): void
    {
        $this->sortiesOrganisees = $sortiesOrganisees;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }



    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }



    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }




    public function getRoles()
    {
        return array($this->role->getValue());
    }
    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }
    public function getSalt(){ return null; }
    public function eraseCredentials(){ return null; }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'mail' => $this->mail,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    public function unserialize(array $serialized): User
    {
        $this->id = $serialized['id'];
        $this->mail = $serialized['mail'];
        $this->username = $serialized['username'];
        $this->password = $serialized['password'];
        return $this;
    }

}
