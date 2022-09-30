<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity=user::class, inversedBy="conversations")
     */
    private $users;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity=Company::class, inversedBy="conversations")
     */
    private $Companies;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="conversation", cascade={"remove"})
     */
    private $messages;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @var array<mixed>|null
     * @ORM\Column(type="json", nullable=true)
     */
    private $talkerDeleted = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->Companies = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            //$this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCompanies(): Collection
    {
        return $this->Companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->Companies->contains($company)) {
            $this->Companies[] = $company;
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        $this->Companies->removeElement($company);

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


    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
          //  if ($message->getConversation() === $this) {
                //$message->setConversation(null);
          //  }
        }

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return mixed[]|null
     */
    public function getTalkerDeleted(): ?array
    {
        return $this->talkerDeleted;
    }

    /**
     * @param array<mixed>|null $talkerDeleted
     * @return $this
     */
    public function setTalkerDeleted(?array $talkerDeleted): self
    {
        $this->talkerDeleted = $talkerDeleted;

        return $this;
    }
}
