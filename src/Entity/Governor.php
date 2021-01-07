<?php

namespace App\Entity;

use App\Exception\GovDataException;
use App\Repository\GovernorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=GovernorRepository::class)
 */
class Governor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $governor_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="governors")
     * @Ignore()
     */
    private $user;

    /**
     * @ORM\OneToMany(
     *     targetEntity=GovernorSnapshot::class,
     *     mappedBy="governor",
     *     orphanRemoval=true,
     *     fetch="LAZY"
     * )
     */
    private $snapshots;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alliance;

    public function __construct()
    {
        $this->snapshots = new ArrayCollection();
    }

    /**
     * @param string $id
     * @param string $status
     * @return Governor
     * @throws GovDataException
     */
    public static function createFromId(string $id, string $status): Governor
    {
        $gov = new self();
        $gov->setGovernorId($id);
        $gov->setStatus($status);

        return $gov;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGovernorId(): ?string
    {
        return $this->governor_id;
    }

    public function setGovernorId(string $governor_id): self
    {
        $this->governor_id = $governor_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection|GovernorSnapshot[]
     */
    public function getSnapshots(): Collection
    {
        return $this->snapshots;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, GovernorStatus::GOV_STATUSES)) {
            throw new GovDataException('Invalid governor status: ' . $status);
        }

        $this->status = $status;

        return $this;
    }

    public function getAlliance(): ?string
    {
        return $this->alliance;
    }

    public function setAlliance(?string $alliance): self
    {
        $this->alliance = $alliance;

        return $this;
    }
}
