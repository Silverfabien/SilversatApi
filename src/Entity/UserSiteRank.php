<?php

namespace App\Entity;

use App\Repository\UserSiteRankRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSiteRankRepository::class)]
class UserSiteRank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'userSiteRanks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Site::class, inversedBy: 'userSiteRanks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne(targetEntity: Rank::class, inversedBy: 'userSiteRanks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rank $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getRole(): ?Rank
    {
        return $this->role;
    }

    public function setRole(?Rank $role): static
    {
        $this->role = $role;

        return $this;
    }
}
