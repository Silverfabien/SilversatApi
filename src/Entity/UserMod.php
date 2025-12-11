<?php

namespace App\Entity;

use App\Enum\UserStatusEnum;
use App\Repository\UserModRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserModRepository::class)]
class UserMod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userMod')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 20, enumType: UserStatusEnum::class)]
    private ?UserStatusEnum $status = UserStatusEnum::ACTIVE;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $statusAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $statusExpirationAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statusReason = null;

    #[ORM\Column]
    private ?bool $accountDeleted = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $accountDeletedAt = null;

    public function __construct()
    {
        $this->accountDeleted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?UserStatusEnum
    {
        return $this->status;
    }

    public function setStatus(UserStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusAt(): ?\DateTimeImmutable
    {
        return $this->statusAt;
    }

    public function setStatusAt(\DateTimeImmutable $statusAt): static
    {
        $this->statusAt = $statusAt;

        return $this;
    }

    public function getStatusExpirationAt(): ?\DateTimeImmutable
    {
        return $this->statusExpirationAt;
    }

    public function setStatusExpirationAt(?\DateTimeImmutable $statusExpirationAt): static
    {
        $this->statusExpirationAt = $statusExpirationAt;

        return $this;
    }

    public function getStatusReason(): ?string
    {
        return $this->statusReason;
    }

    public function setStatusReason(?string $statusReason): static
    {
        $this->statusReason = $statusReason;

        return $this;
    }

    public function isAccountDeleted(): ?bool
    {
        return $this->accountDeleted;
    }

    public function setAccountDeleted(bool $accountDeleted): static
    {
        $this->accountDeleted = $accountDeleted;

        return $this;
    }

    public function getAccountDeletedAt(): ?\DateTimeImmutable
    {
        return $this->accountDeletedAt;
    }

    public function setAccountDeletedAt(?\DateTimeImmutable $accountDeletedAt): static
    {
        $this->accountDeletedAt = $accountDeletedAt;

        return $this;
    }
}
