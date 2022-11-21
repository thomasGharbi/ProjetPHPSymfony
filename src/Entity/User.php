<?php

namespace App\Entity;

use Cassandra\Exception\ValidationException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\UserRepository;
use Stringable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Exception\RuntimeException;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="Cette adresse email est déjà associé à un compte existant")
 * @UniqueEntity("phone", message="Ce numéro de téléphone est déjà associé à un compte existant")
 * @UniqueEntity("username", message="Ce nom d'utilisateur est déjà pris")
 * @ORM\Table(name="User", indexes={@ORM\Index(columns={"email",
 * "username","google_id", "github_id", "phone", "uuid"}, flags={"fulltext"})})
 * 
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var array|string[]
     */
    public static array $rolesUserList = ['ROLE_USER','ROLE_ADMIN'];
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="L'adresse email doit être saisi")
     * @Assert\Email(message="Cette adresse email n'est pas valide")
     */
    private string $email;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     * 
     */
    private array $roles = [];

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom d'utilisateur doit être saisi")
     * @Assert\Regex(pattern = "/^[A-Za-z][A-Za-z0-9]{5,30}$/",
     *               message = "Votre nom d'utilisateur doit comprendre entre 5 et 30 caractère et contenir uniquement des lettres des chiffres")
     *
     */
    private string $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le mot de passe doit être saisi")
     * @Assert\Regex(pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,50}$/",
     *               message = "Le mot de passe doit contenir au moins: huit caractères dont une lettre, un chiffre et un caractère spécial(@$!%*?&)")
     *
     */
    private string $password;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max = 50,
     *      maxMessage = "Le nom ne peut pas contenir moins de {{ limit }} caractères")
     *
     */
    private ?string $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max = 50,
     *      maxMessage = "Le prènom ne peut pas contenir moins de {{ limit }} caractères")
     *
     */
    private ?string $firstName;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max = 20)
     */
    private ?string $birth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern = "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/", 
     *               message = "le numéro de téléphone saisit n'est pas valide")
     */
    private ?string $phone;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max = 250)
     * 
     */
    private ?string $profileImage;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 20)
     */
    private string $gender;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean" )
     */
    private bool $isVerified = false;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $registrationToken;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $accountMustBeVerifiedBefore;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $accountVerifiedAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $passwordModified = false;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $passwordModifiedAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string|null $forgotPasswordToken;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $forgotPasswordMustBeVerifiedBefore;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $ForgotPasswordRequestedAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $googleID;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $githubID;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity=Company::class, mappedBy="user")
     */
    private $companies;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity=Notices::class, mappedBy="user")
     */
    private $notices;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity=Conversation::class, mappedBy="users")
     */
    private $conversations;




    public function __construct(){
        $this->profileImage = '/uploads/profile_image_default/user_profil_image_default.jpg';
        $this->companies = new ArrayCollection();
        $this->notices = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->uuid = Uuid::v1();
        $this->roles = ['ROLE_USER'];
        $this->gender = 'non-précisé';
    }

    

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     *
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @return array<string>
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return void
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirth(): ?string
    {
        return $this->birth;
    }

    public function setBirth(string $birth): self
    {
        $this->birth = $birth;

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

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(string $profilImage): self
    {
        $this->profileImage = $profilImage;

        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getRegistrationToken(): ?string
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?string $registrationToken): self
    {
        $this->registrationToken = $registrationToken;

        return $this;
    }

    public function getAccountMustBeVerifiedBefore(): ?\DateTimeImmutable
    {
        return $this->accountMustBeVerifiedBefore;
    }

    public function setAccountMustBeVerifiedBefore(?\DateTimeImmutable $accountMustBeVerifiedBefore): self
    {
        $this->accountMustBeVerifiedBefore = $accountMustBeVerifiedBefore;

        return $this;
    }

    public function getAccountVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->accountVerifiedAt;
    }

    public function setAccountVerifiedAt(?\DateTimeImmutable $accountVerifiedAt): self
    {
        $this->accountVerifiedAt = $accountVerifiedAt;

        return $this;
    }

    public function getPasswordModified(): bool
    {
        return $this->passwordModified;
    }

    public function setPasswordModified(bool $passwordModified): self
    {
        $this->passwordModified = $passwordModified;

        return $this;
    }

    public function getPasswordModifiedAt(): ?\DateTimeImmutable
    {
        return $this->passwordModifiedAt;
    }

    public function setPasswordModifiedAt(?\DateTimeImmutable $passwordModifiedAt): self
    {
        $this->passwordModifiedAt = $passwordModifiedAt;

        return $this;
    }

    public function getForgotPasswordToken(): ?string
    {
        return $this->forgotPasswordToken;
    }

    public function setForgotPasswordToken(?string $forgotPasswordToken): self
    {
        $this->forgotPasswordToken = $forgotPasswordToken;

        return $this;
    }

    public function getForgotPasswordMustBeVerifiedBefore(): ?\DateTimeImmutable
    {
        return $this->forgotPasswordMustBeVerifiedBefore;
    }

    public function setForgotPasswordMustBeVerifiedBefore(?\DateTimeImmutable $forgotPasswordMustBeVerifiedBefore): self
    {
        $this->forgotPasswordMustBeVerifiedBefore = $forgotPasswordMustBeVerifiedBefore;

        return $this;
    }

    public function getForgotPasswordRequestedAt(): ?\DateTimeImmutable
    {
        return $this->ForgotPasswordRequestedAt;
    }

    public function setForgotPasswordRequestedAt(?\DateTimeImmutable $ForgotPasswordRequestedAt): self
    {
        $this->ForgotPasswordRequestedAt = $ForgotPasswordRequestedAt;

        return $this;
    }

    public function getGoogleID(): ?string
    {
        return $this->googleID;
    }

    public function setGoogleID(?string $googleID): self
    {
        $this->googleID = $googleID;

        return $this;
    }

    public function getGithubID(): ?string
    {
        return $this->githubID;
    }

    public function setGithubID(?string $githubID): self
    {
        $this->githubID = $githubID;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setUser($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getUser() === $this) {
                $company->setUser(null);
            }
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
            $notice->setUser($this);
        }

        return $this;
    }

    public function removeNotice(Notices $notice): self
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getUser() === $this) {
                $notice->setUser(null);
            }
        }

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
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
        }

        return $this;
    }

}
