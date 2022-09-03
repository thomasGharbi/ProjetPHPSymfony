<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use ContainerXLbvlVp\getSecurity_Logout_Listener_CsrfTokenClearingService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 * @UniqueEntity("SIRETNumber", message="Ce numéro SIRET n'est pas valide")
 */
class Company
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="companies")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le Nom de l'entreprise doit être saisi.")
     * @Assert\Length(max = 50,
     *      maxMessage = "Le nom de l'entrprise ne peut pas contenir moins de {{ limit }} caractères")
     */
    private $nameOfCompany;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern = "/^\d{14}$/", message = "Le numéro SIRET inscrit n'est pas valide")
     * @Assert\NotBlank(message="Le numéro SIRET doit être saisi.")
     */
    private $SIRETNumber;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom et prenom du ou des responsables doit être saisi.")
     * @Assert\Length(min = 2, max = 50,
     *      minMessage = "Le nom et prenom du ou des responsables doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "Le nom et prenom du ou des responsables ne doit contenir moins de {{ limit }} caractères")
     */
    private $nameOfCompanyManager;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom et prenom du ou des responsables doit être saisi.")
     * @Assert\Length(min = 2, max = 50,
     *      minMessage = "Le nom et prenom du ou des responsables doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "Le nom et prenom du ou des responsables ne peut pas contenir moins de {{ limit }} caractères")
     */
    private $firstnameOfCompanyManager;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le secteur d'activité doit être saisi.")
     * @Assert\Length(min = 2, max = 50,
     *      minMessage = " Le secteur d'activité doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "Le secteur d'activité ne doit pas contenir moins de {{ limit }} caractères")
     */
    private $sector;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min = 2, max = 50,
     *      minMessage = "La Spécialisation doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "La Spécialisation ne doit contenir moins de {{ limit }} caractères")
     *
     */
    private $specialization;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'année de debut d'activité La zone maximal d'activité doit être saisi.")
     * @Assert\Regex(pattern = "/^\d{4}$/", message = "l'année de début d'activité n'est pas valide")
     *
     */
    private $inActivitySince;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min = 2, max = 100,
     *      minMessage = "l'adresse de l'entreprise doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "l'adresse de l'entreprise ne doit contenir moins de {{ limit }} caractères")
     */
    private $address;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max = 50)
     *
     */
    private $department;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La ville doit être saisi.")
     * @Assert\Length(max = 50,
     *      maxMessage = "La ville ne peut pas contenir moins de {{ limit }} caractères")
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le code postal doit être saisi.")
     * @Assert\Regex(pattern = "/^\d{5}$/", message = "le code postal saisi est invalide")
     */
    private $postalCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La zone maximal d'activité doit être saisi.")
     * @Assert\Length(min = 3,max = 50,)
     */
    private $areaActivity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le Titre lié a votre entreprise doit être saisi.")
     * @Assert\Length(
     *      min = 10,
     *      max = 80,
     *      minMessage = "Le titre doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "Le titre ne peut pas contenir moins de {{ limit }} caractères")
     */
    private $profileTitle;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La description doit être saisi.")
     * @Assert\Length(
     *      min = 30,
     *      max = 250,
     *      minMessage = "La description doit contenir plus de {{ limit }} caractères",
     *      maxMessage = "La description ne peut pas contenir moins de {{ limit }} caractères")
     */
    private $profileDescription;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="La photo de profil est obligatoire")
     * @Assert\Length(max = 255)
     */
    private $profileImage;

    /**
     * @var array<string>
     * @ORM\Column(type="simple_array", nullable=true)
     *
     */
    private $images = [];

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $generalNotice = 8;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $qualityNotice;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $speedNotice;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $priceNotice;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity=Notices::class, mappedBy="company")
     */
    private $notices;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern = "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/",
     *               message = "le numéro de téléphone saisit n'est pas valide")
     */
    private $phone;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(message="Cette adresse email n'est pas valide", mode="strict")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity=Conversation::class, mappedBy="Companies")
     */
    private $conversations;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countNotice;

    public function __construct()
    {

        $this->notices = new ArrayCollection();
        $this->generalNotice = 0;
        $this->qualityNotice = 0;
        $this->speedNotice = 0;
        $this->priceNotice = 0;
        $this->conversations = new ArrayCollection();
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNameOfCompany(): string
    {
        return $this->nameOfCompany;
    }


    public function setNameOfCompany(string $nameOfCompany ): self
    {


            $this->nameOfCompany = $nameOfCompany;



        return $this;
    }

    public function getSIRETNumber(): string
    {
        return $this->SIRETNumber;
    }

    public function setSIRETNumber(string $SIRETNumber ): self
    {

            $this->SIRETNumber = $SIRETNumber;



        return $this;
    }

    public function getNameOfCompanyManager(): string
    {
        return $this->nameOfCompanyManager;
    }

    public function setNameOfCompanyManager(string $nameOfCompanyManager): self
    {

            $this->nameOfCompanyManager = $nameOfCompanyManager;



        return $this;
    }

    public function getFirstnameOfCompanyManager(): string
    {
        return $this->firstnameOfCompanyManager;
    }

    public function setFirstnameOfCompanyManager(string $firstnameOfCompanyManager): self
    {

            $this->firstnameOfCompanyManager = $firstnameOfCompanyManager;



        return $this;
    }

    public function getSector(): string
    {
        return $this->sector;
    }

    public function setSector(string $sector ): self
    {

            $this->sector = $sector;



        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }


    public function setSpecialization(?string $specialization ): self
    {

            $this->specialization = $specialization;



        return $this;
    }

    public function getInActivitySince(): string
    {
        return $this->inActivitySince;
    }

    public function setInActivitySince(string $inActivitySince): self
    {

            $this->inActivitySince = $inActivitySince;

        

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {

            $this->address = $address;


        
        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {

            $this->department = $department; 

        

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {

            $this->city = $city;

        

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {

            $this->postalCode = $postalCode;


        return $this;
    }

    public function getAreaActivity(): string
    {
        return $this->areaActivity;
    }

    public function setAreaActivity(string $areaActivity): self
    {

            $this->areaActivity = $areaActivity;



        return $this;
    }

    public function getProfileTitle(): string
    {
        return $this->profileTitle;
    }

    public function setProfileTitle(string $profileTitle ): self
    {

            $this->profileTitle = $profileTitle;


        return $this;
    }

    public function getProfileDescription(): string
    {
        return $this->profileDescription;
    }

    public function setProfileDescription(string $profileDescription): self
    {

            $this->profileDescription = $profileDescription;



        return $this;
    }

    public function getProfileImage(): string
    {
        return $this->profileImage;
    }

    public function setProfileImage(string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array<string> $images
     * @return $this
     */
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getGeneralNotice(): ?float
    {

        return $this->generalNotice;
    }

    public function setGeneralNotice(float $generalNotice, bool $average = true): self
    {

        if($average) {
            $this->generalNotice = ($this->generalNotice * ($this->getCountNotice() - 1) + $generalNotice) / ($this->getCountNotice());

        }else {
            $this->generalNotice = $generalNotice;
        }


        return $this;
    }

    public function getQualityNotice(): ?float
    {
        return $this->qualityNotice;
    }

    public function setQualityNotice(float $qualityNotice,bool $average = true ): self
    {
        if($average){
            $this->qualityNotice = ($this->qualityNotice * ($this->getCountNotice() - 1) + $qualityNotice) / ($this->getCountNotice());
        }else{
            $this->qualityNotice = $qualityNotice;
        }

        return $this;
    }

    public function getSpeedNotice(): ?float
    {
        return $this->speedNotice;
    }

    public function setSpeedNotice(float $speedNotice, bool $average = true ): self
    {

        if ($average){
            $this->speedNotice = ($this->speedNotice * ($this->getCountNotice() - 1) + $speedNotice) / ($this->getCountNotice());
        }else{
            $this->speedNotice = $speedNotice;
        }

        return $this;
    }

    public function getPriceNotice(): ?float
    {
        return $this->priceNotice;
    }

    public function setPriceNotice(float $priceNotice,  bool $average = true ): self
    {
        if ($average){
            $this->priceNotice = ($this->priceNotice * ($this->getCountNotice() - 1) + $priceNotice) / ($this->getCountNotice());
        } else {
            $this->priceNotice = $priceNotice;
        }
        return $this;
    }

    /**
     * @return Collection|Notices[]
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function addNotice(Notices $notice): self
    {
        if (!$this->notices->contains($notice)) {
            $this->notices[] = $notice;
            $notice->setCompany($this);
        }

        return $this;
    }

    public function removeNotice(Notices $notice): self
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getCompany() === $this) {
                $notice->setCompany(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {

            $this->phone = $phone;



        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {

            $this->email = $email;



        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->addCompany($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeCompany($this);
        }

        return $this;
    }

    public function getCountNotice(): ?int
    {
        return $this->countNotice;
    }

    public function setCountNotice(?int $countNotice): self
    {
        $this->countNotice += $countNotice;

        return $this;
    }
}
