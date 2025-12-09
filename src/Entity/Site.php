<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    /**
     * @var Collection<int, UserSiteRank>
     */
    #[ORM\OneToMany(targetEntity: UserSiteRank::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $userSiteRanks;

    public function __construct()
    {
        $this->userSiteRanks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $userSiteRank->setSite($this);
        }

        return $this;
    }

    public function removeUserSiteRank(UserSiteRank $userSiteRank): static
    {
        if ($this->userSiteRanks->removeElement($userSiteRank)) {
            // set the owning side to null (unless already changed)
            if ($userSiteRank->getSite() === $this) {
                $userSiteRank->setSite(null);
            }
        }

        return $this;
    }
}
