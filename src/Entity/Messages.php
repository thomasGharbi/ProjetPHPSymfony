<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 */
class Messages
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Conversation
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="messages")
     */
    private $conversation;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $is_read;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @var null|User
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     */
    private $userOwner;

    /**
     * @var null|Company
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="messages")
     */
    private $companyOwner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function setConversation(Conversation $conversation): self
    {
        $this->conversation = $conversation;

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

    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read = false): self
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUserOwner(): ?User
    {
        return $this->userOwner;
    }

    public function setUserOwner(User $userOwner): self
    {
        $this->userOwner = $userOwner;

        return $this;
    }

    public function getCompanyOwner(): ?Company
    {
        return $this->companyOwner;
    }

    public function setCompanyOwner(Company $companyOwner): self
    {
        $this->companyOwner = $companyOwner;

        return $this;
    }
}
