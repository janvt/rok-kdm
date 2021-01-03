<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Governor::class, mappedBy="user_id")
     */
    private $governors;

    public function __construct()
    {
        $this->governors = new ArrayCollection();
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
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
            $governor->setUserId($this);
        }

        return $this;
    }

    public function removeGovernor(Governor $governor): self
    {
        if ($this->governors->removeElement($governor)) {
            // set the owning side to null (unless already changed)
            if ($governor->getUserId() === $this) {
                $governor->setUserId(null);
            }
        }

        return $this;
    }
}
