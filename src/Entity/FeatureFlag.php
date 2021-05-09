<?php

namespace App\Entity;

use App\Repository\FeatureFlagRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeatureFlagRepository::class)
 */
class FeatureFlag
{
    const COMMANDERS = 'commanders';
    const EQUIPMENT = 'equipment';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uid;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getRoles(): array
    {
        if (!$this->roles) {
            return [];
        }

        return array_unique(array_map('trim', explode(',', $this->roles)));
    }

    public function setRoles($roles): self
    {
        $this->roles = is_array($roles) ? implode(',', $roles) : $roles;

        return $this;
    }
}
