<?php

namespace App\Entity;

use App\Repository\VisitorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisitorRepository::class)
 */
class Visitor
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var null|User
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private $user;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $visitedAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userIp;

    /**
     * @var array<mixed>
     * @ORM\Column(type="json")
     */
    private $actions = [];

    public function __construct()
    {
        $this->visitedAt = new \DateTimeImmutable('NOW');
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getVisitedAt(): ?\DateTimeImmutable
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeImmutable $visitedAt): self
    {
        $this->visitedAt = $visitedAt;

        return $this;
    }

    public function getUserIp(): ?string
    {
        return $this->userIp;
    }

    public function setUserIp(?string $userIp): self
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * @return mixed[]|null
     */
    public function getActions(): ?array
    {
        return $this->actions;
    }

    /**
     * @param array<mixed> $actions
     * @return $this
     */
    public function setActions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }
}
