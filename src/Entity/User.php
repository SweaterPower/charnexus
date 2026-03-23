<?php

namespace App\Entity;

use App\Entity\CampaignInvite;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Campaign>
     */
    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'userId', orphanRemoval: true)]
    private Collection $campaigns;

    /**
     * @var Collection<int, Character>
     */
    #[ORM\OneToMany(targetEntity: Character::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $characters;

    /**
     * @var Collection<int, CampaignInvite>
     */
    #[ORM\OneToMany(targetEntity: CampaignInvite::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $sentInvites;

    /**
     * @var Collection<int, CampaignInvite>
     */
    #[ORM\OneToMany(targetEntity: CampaignInvite::class, mappedBy: 'recipient', orphanRemoval: true)]
    private Collection $receivedInvites;

    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
        $this->characters = new ArrayCollection();
        $this->sentInvites = new ArrayCollection();
        $this->receivedInvites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function addCampaign(Campaign $campaign): static
    {
        if (!$this->campaigns->contains($campaign)) {
            $this->campaigns->add($campaign);
            $campaign->setUserId($this);
        }

        return $this;
    }

    public function removeCampaign(Campaign $campaign): static
    {
        if ($this->campaigns->removeElement($campaign)) {
            // set the owning side to null (unless already changed)
            if ($campaign->getUserId() === $this) {
                $campaign->setUserId(null);
            }
        }

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
            $character->setUser($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): static
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getUser() === $this) {
                $character->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CampaignInvite>
     */
    public function getSentInvites(): Collection
    {
        return $this->sentInvites;
    }

    public function addSentInvite(CampaignInvite $sentInvite): static
    {
        if (!$this->sentInvites->contains($sentInvite)) {
            $this->sentInvites->add($sentInvite);
            $sentInvite->setSender($this);
        }

        return $this;
    }

    public function removeSentInvite(CampaignInvite $sentInvite): static
    {
        if ($this->sentInvites->removeElement($sentInvite)) {
            // set the owning side to null (unless already changed)
            if ($sentInvite->getSender() === $this) {
                $sentInvite->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CampaignInvite>
     */
    public function getReceivedInvites(): Collection
    {
        return $this->receivedInvites;
    }

    public function addReceivedInvite(CampaignInvite $receivedInvite): static
    {
        if (!$this->receivedInvites->contains($receivedInvite)) {
            $this->receivedInvites->add($receivedInvite);
            $receivedInvite->setRecipient($this);
        }

        return $this;
    }

    public function removeRecievedInvite(CampaignInvite $receivedInvite): static
    {
        if ($this->receivedInvites->removeElement($receivedInvite)) {
            // set the owning side to null (unless already changed)
            if ($receivedInvite->getRecipient() === $this) {
                $receivedInvite->setRecipient(null);
            }
        }

        return $this;
    }
}
