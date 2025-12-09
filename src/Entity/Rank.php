<?php

namespace App\Entity;

use App\Repository\RankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    /**
     * @var Collection<int, UserSiteRank>
     */
    #[ORM\OneToMany(targetEntity: UserSiteRank::class, mappedBy: 'role', orphanRemoval: true)]
    private Collection $userSiteRanks;

    public function __construct()
    {
        $this->userSiteRanks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRolename(): ?string
    {
        return $this->rolename;
    }

    public function setRolename(string $rolename): static
    {
        $this->rolename = $rolename;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, UserSiteRank>
     */
    public function getUserSiteRanks(): Collection
    {
        return $this->userSiteRanks;
    }

    public function addUserSiteRank(UserSiteRank $userSiteRank): static
    {
        if (!$this->userSiteRanks->contains($userSiteRank)) {
            $this->userSiteRanks->add($userSiteRank);
            $userSiteRank->setRole($this);
        }

        return $this;
    }

    public function removeUserSiteRank(UserSiteRank $userSiteRank): static
    {
        if ($this->userSiteRanks->removeElement($userSiteRank)) {
            // set the owning side to null (unless already changed)
            if ($userSiteRank->getRole() === $this) {
                $userSiteRank->setRole(null);
            }
        }

        return $this;
    }
}
