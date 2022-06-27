<?php

namespace App\Entity;

use App\Repository\NoticesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoticesRepository::class)
 */
class Notices
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
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="notices")
     */
    private $user;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity=company::class, inversedBy="notices")
     */
    private $company;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviceType;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $servicePlace;

    /**
     * @var string
     * @ORM\Column(type="string", length=10000)
     */
    private $comment;

    /**
     * @var array<mixed>
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $generalNotice;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $qualityNotice;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $speedNotice;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $priceNotice;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $modified;

    /**
     * @var \DateTimeInterface|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;

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

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    public function setServiceType(?string $serviceType): self
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    public function getServicePlace(): ?string
    {
        return $this->servicePlace;
    }

    public function setServicePlace(?string $servicePlace): self
    {
        $this->servicePlace = $servicePlace;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return mixed[]
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

    public function getGeneralNotice(): ?int
    {
        return $this->generalNotice;
    }

    public function setGeneralNotice(int $generalNotice): self
    {
        $this->generalNotice = $generalNotice;

        return $this;
    }

    public function getQualityNotice(): ?int
    {
        return $this->qualityNotice;
    }

    public function setQualityNotice(int $qualityNotice): self
    {
        $this->qualityNotice = $qualityNotice;

        return $this;
    }

    public function getSpeedNotice(): ?int
    {
        return $this->speedNotice;
    }

    public function setSpeedNotice(int $speedNotice): self
    {
        $this->speedNotice = $speedNotice;

        return $this;
    }

    public function getPriceNotice(): ?int
    {
        return $this->priceNotice;
    }

    public function setPriceNotice(int $priceNotice): self
    {
        $this->priceNotice = $priceNotice;

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

    public function getModified(): ?bool
    {
        return $this->modified;
    }

    public function setModified(bool $modified): self
    {
        $this->modified = $modified;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
