<?php

namespace App\Entity;

use App\Enum\RoleEnum;
use App\Repository\RankRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankRepository::class)]
class Rank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $rolename = null;

    #[ORM\Column(type: 'string', length: 255, enumType: RoleEnum::class)]
    private ?RoleEnum $role = RoleEnum::USER;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRolename(): ?string
    {
        return $this->rolename;
    }

    public function getRole(): ?RoleEnum
    {
        return $this->role;
    }

    public function setRole(RoleEnum $role): self
    {
        $this->role = $role;
        $this->rolename = $role?->label();

        return $this;
    }
}
