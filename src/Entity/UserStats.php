<?php

namespace App\Entity;

use App\Repository\UserStatsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserStatsRepository::class)]
class UserStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userStats')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastPasswordChangedAt = null;

    #[ORM\Column]
    private ?int $loginAttempts = null;

    #[ORM\Column]
    private ?int $numberOfBlocked = null;

    #[ORM\Column]
    private ?int $numberOfBanned = null;

    #[ORM\Column(length: 255)]
    private ?string $registerOn = null;

    #[ORM\Column(length: 255)]
    private ?string $loginOn = null;

    public function __construct()
    {
        $this->numberOfBlocked = 0;
        $this->numberOfBanned = 0;
        $this->loginAttempts = 0;
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

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getLastPasswordChangedAt(): ?\DateTimeImmutable
    {
        return $this->lastPasswordChangedAt;
    }

    public function setLastPasswordChangedAt(?\DateTimeImmutable $lastPasswordChangedAt): static
    {
        $this->lastPasswordChangedAt = $lastPasswordChangedAt;

        return $this;
    }

    public function getLoginAttempts(): ?int
    {
        return $this->loginAttempts;
    }

    public function setLoginAttempts(int $loginAttempts): static
    {
        $this->loginAttempts = $loginAttempts;

        return $this;
    }

    public function getNumberOfBlocked(): ?int
    {
        return $this->numberOfBlocked;
    }

    public function setNumberOfBlocked(int $numberOfBlocked): static
    {
        $this->numberOfBlocked = $numberOfBlocked;

        return $this;
    }

    public function getNumberOfBanned(): ?int
    {
        return $this->numberOfBanned;
    }

    public function setNumberOfBanned(int $numberOfBanned): static
    {
        $this->numberOfBanned = $numberOfBanned;

        return $this;
    }

    public function getRegisterOn(): ?string
    {
        return $this->registerOn;
    }

    public function setRegisterOn(string $registerOn): static
    {
        $this->registerOn = $registerOn;

        return $this;
    }

    public function getLoginOn(): ?string
    {
        return $this->loginOn;
    }

    public function setLoginOn(string $loginOn): static
    {
        $this->loginOn = $loginOn;

        return $this;
    }
}
