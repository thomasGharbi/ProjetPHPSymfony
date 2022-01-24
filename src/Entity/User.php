<?php

namespace App\Entity;

use Cassandra\Exception\ValidationException;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\UserRepository;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Exception\RuntimeException;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="Cette adresse email n'est pas valide")
 * @UniqueEntity("phone", message="Ce numéro n'est pas valide")
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
     * 
     * 
     * 
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(message="Cette adresse email n'est pas valide")
     *
     * 
     * 
     */
    private string $email;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     * 
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @ORM\Column(type="string")
     *
     *
     */
    private string $password;

    /**
     * @var string 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $birth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern = "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/", 
     *               message = "le numéro de téléphone saisit n'est pas valide")
     */
    private string|null $phone;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * 
     * 
     */
    private string|null $profilImage;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
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
    private string|null $registrationToken;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $accountMustBeVerifiedBefore;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable|null $accountVerifiedAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $passwordModified = false;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable|null $passwordModifiedAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string|null $forgotPasswordToken;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable|null $forgotPasswordMustBeVerifiedBefore;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable|null $ForgotPasswordRequestedAt;


    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @return array<string>
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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

    public function getProfilImage(): ?string
    {
        return $this->profilImage;
    }

    public function setProfilImage(string $profilImage): self
    {
        $this->profilImage = $profilImage;

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

    public function setAccountMustBeVerifiedBefore(\DateTimeImmutable $accountMustBeVerifiedBefore): self
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
}
