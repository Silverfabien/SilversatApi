<?php

namespace App\Entity;

use App\Repository\UserSecurityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSecurityRepository::class)]
class UserSecurity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userSecurity')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmationToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $confirmationTokenExpirationAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordTokenExpirationAt = null;

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

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): static
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getConfirmationTokenExpirationAt(): ?\DateTimeImmutable
    {
        return $this->confirmationTokenExpirationAt;
    }

    public function setConfirmationTokenExpirationAt(?\DateTimeImmutable $confirmationTokenExpirationAt): static
    {
        $this->confirmationTokenExpirationAt = $confirmationTokenExpirationAt;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetPasswordTokenExpirationAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordTokenExpirationAt;
    }

    public function setResetPasswordTokenExpirationAt(?\DateTimeImmutable $resetPasswordTokenExpirationAt): static
    {
        $this->resetPasswordTokenExpirationAt = $resetPasswordTokenExpirationAt;

        return $this;
    }
}
