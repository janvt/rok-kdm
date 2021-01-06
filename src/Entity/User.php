<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Governor::class, mappedBy="user")
     */
    private $governors;

    /**
     * @ORM\Column(type="string", length=65535, nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discordId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discordUsername;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discordDiscriminator;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discordAvatarHash;

    public function __construct()
    {
        $this->governors = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getEmail();
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->email === $user->getUsername() && $this->getSalt() === $user->getSalt();
    }

    public function discordDisplayName(): string
    {
        return $this->discordUsername . '#' . $this->discordDiscriminator;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Governor[]
     */
    public function getGovernors(): Collection
    {
        return $this->governors;
    }

    public function addGovernor(Governor $governor): self
    {
        if (!$this->governors->contains($governor)) {
            $this->governors[] = $governor;
            $governor->setUser($this);
        }

        return $this;
    }

    public function removeGovernor(Governor $governor): self
    {
        if ($this->governors->removeElement($governor)) {
            // set the owning side to null (unless already changed)
            if ($governor->getUser() === $this) {
                $governor->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = array_map('trim', explode(',', $this->roles));
        $roles[] = ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles($roles): self
    {
        $this->roles = is_array($roles) ? implode(',', $roles) : $roles;

        return $this;
    }

    public function getSalt()
    {
        return 'horse';
    }

    public function eraseCredentials()
    {
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getPassword()
    {
        return null;
    }

    public function getDiscordUsername(): ?string
    {
        return $this->discordUsername;
    }

    public function setDiscordUsername(?string $discordUsername): self
    {
        $this->discordUsername = $discordUsername;

        return $this;
    }

    public function getDiscordDiscriminator(): ?string
    {
        return $this->discordDiscriminator;
    }

    public function setDiscordDiscriminator(?string $discordDiscriminator): self
    {
        $this->discordDiscriminator = $discordDiscriminator;

        return $this;
    }

    public function getDiscordAvatarHash(): ?string
    {
        return $this->discordAvatarHash;
    }

    public function setDiscordAvatarHash(?string $discordAvatarHash): self
    {
        $this->discordAvatarHash = $discordAvatarHash;

        return $this;
    }
}
