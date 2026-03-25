<?php

namespace App\Entity;

use App\Repository\CampaignRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
#[Broadcast]
class Campaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'campaigns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Character>
     */
    #[ORM\OneToMany(targetEntity: Character::class, mappedBy: 'campaign')]
    private Collection $characters;

    /**
     * @var Collection<int, CampaignRole>
     */
    #[ORM\OneToMany(targetEntity: CampaignRole::class, mappedBy: 'campaign', orphanRemoval: true)]
    private Collection $roles;

    /**
     * @var Collection<int, CampaignEvent>
     */
    #[ORM\OneToMany(targetEntity: CampaignEvent::class, mappedBy: 'campaign', orphanRemoval: true)]
    private Collection $events;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Character>
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): static
    {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
            $character->setCampaign($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): static
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getCampaign() === $this) {
                $character->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CampaignRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(CampaignRole $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setCampaign($this);
        }

        return $this;
    }

    public function removeRole(CampaignRole $role): static
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getCampaign() === $this) {
                $role->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CampaignEvent>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(CampaignEvent $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCampaign($this);
        }

        return $this;
    }

    public function removeEvent(CampaignEvent $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCampaign() === $this) {
                $event->setCampaign(null);
            }
        }

        return $this;
    }
}
