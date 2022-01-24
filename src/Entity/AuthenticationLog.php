<?php

namespace App\Entity;

use App\Repository\AuthenticationLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AuthenticationLogRepository::class)
 *
 */
class AuthenticationLog
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $authAttemptAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $emailEntered;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $userIp;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $authSuccessful;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $blackListed = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $blackListedUntil;

    public function __construct(string $userIp, string $emailEntered,bool $authSuccessful)
    {
        $this->authAttemptAt = new \DateTimeImmutable('NOW');
        $this->emailEntered = $emailEntered;
        $this->userIp = $userIp;
        $this->authSuccessful = $authSuccessful;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthAttemptAt(): ?\DateTimeImmutable
    {
        return $this->authAttemptAt;
    }

    public function setAuthAttemptAt(\DateTimeImmutable $authAttemptAt): self
    {
        $this->authAttemptAt = $authAttemptAt;

        return $this;
    }

    public function getEmailEntered(): ?string
    {
        return $this->emailEntered;
    }

    public function setEmailEntered(string $emailEntered): self
    {
        $this->emailEntered = $emailEntered;

        return $this;
    }

    public function getUserIp(): ?string
    {
        return $this->userIp;
    }

    public function setUserIp(string $userIp): self
    {
        $this->userIp = $userIp;

        return $this;
    }

    public function getAuthSuccessful(): ?bool
    {
        return $this->authSuccessful;
    }

    public function setAuthSuccessful(bool $authSuccessful): self
    {
        $this->authSuccessful = $authSuccessful;

        return $this;
    }

    public function getBlackListed(): ?bool
    {
        return $this->blackListed;
    }

    public function setBlackListed(bool $blackListed): self
    {
        $this->blackListed = $blackListed;

        return $this;
    }

    public function getBlackListedUntil(): ?\DateTime
    {
        return $this->blackListedUntil;
    }

    public function setBlackListedUntil(?\DateTime $blackListedUntil): self
    {
        $this->blackListedUntil = $blackListedUntil;

        return $this;
    }
}
