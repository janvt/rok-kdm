<?php

namespace App\Entity;

use App\Exception\GovDataException;
use App\Repository\GovernorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
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
     * @Ignore()
     */
    private $snapshots;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=OfficerNote::class, mappedBy="governor", orphanRemoval=true)
     * @Ignore()
     */
    private $officerNotes;

    /**
     * @ORM\ManyToOne(targetEntity=Alliance::class)
     */
    private $alliance;

    /**
     * @ORM\OneToMany(
     *     targetEntity=Commander::class,
     *     mappedBy="governor",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @OrderBy({"uid": "ASC"})
     */
    private $commanders;

    /**
     * @ORM\OneToMany(
     *     targetEntity=Equipment::class,
     *     mappedBy="governor",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $equipment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $altNames;

    /**
     * @ORM\OneToMany(targetEntity=EquipmentLoadout::class, mappedBy="governor", orphanRemoval=true)
     */
    private $equipmentLoadouts;

    public function __construct()
    {
        $this->snapshots = new ArrayCollection();
        $this->officerNotes = new ArrayCollection();
        $this->commanders = new ArrayCollection();
        $this->equipment = new ArrayCollection();
        $this->equipmentLoadouts = new ArrayCollection();
    }

    /**
     * @param string $id
     * @return Governor
     * @throws GovDataException
     */
    public static function createFromId(string $id): Governor
    {
        $gov = new self();
        $gov->setGovernorId($id);
        $gov->setStatus(GovernorStatus::STATUS_UNKNOWN);

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

    /**
     * @param string $status
     * @return $this
     * @throws GovDataException
     */
    public function setStatus(string $status): self
    {
        $status = strtolower($status);
        if (!in_array($status, GovernorStatus::GOV_STATUSES)) {
            throw new GovDataException('Invalid governor status: ' . $status);
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|OfficerNote[]
     */
    public function getOfficerNotes(): Collection
    {
        return $this->officerNotes;
    }

    public function addOfficerNote(OfficerNote $officerNote): self
    {
        if (!$this->officerNotes->contains($officerNote)) {
            $this->officerNotes[] = $officerNote;
            $officerNote->setGovernor($this);
        }

        return $this;
    }

    public function removeOfficerNote(OfficerNote $officerNote): self
    {
        if ($this->officerNotes->removeElement($officerNote)) {
            // set the owning side to null (unless already changed)
            if ($officerNote->getGovernor() === $this) {
                $officerNote->setGovernor(null);
            }
        }

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): self
    {
        $this->alliance = $alliance;

        return $this;
    }

    /**
     * @return Collection|Commander[]
     */
    public function getCommanders(): Collection
    {
        return $this->commanders;
    }

    public function addCommander(Commander $commander): self
    {
        if (!$this->commanders->contains($commander)) {
            $this->commanders[] = $commander;
            $commander->setGovernor($this);
        }

        return $this;
    }

    public function removeCommander(Commander $commander): self
    {
        if ($this->commanders->removeElement($commander)) {
            // set the owning side to null (unless already changed)
            if ($commander->getGovernor() === $this) {
                $commander->setGovernor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
            $equipment->setGovernor($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getGovernor() === $this) {
                $equipment->setGovernor(null);
            }
        }

        return $this;
    }

    public function getAltNames(): ?string
    {
        return $this->altNames;
    }

    public function setAltNames(?string $altNames): self
    {
        $this->altNames = $altNames;

        return $this;
    }

    /**
     * @return Collection|EquipmentLoadout[]
     */
    public function getEquipmentLoadouts(): Collection
    {
        return $this->equipmentLoadouts;
    }

    public function addEquipmentLoadout(EquipmentLoadout $equipmentLoadout): self
    {
        if (!$this->equipmentLoadouts->contains($equipmentLoadout)) {
            $this->equipmentLoadouts[] = $equipmentLoadout;
            $equipmentLoadout->setGovernor($this);
        }

        return $this;
    }

    public function removeEquipmentLoadout(EquipmentLoadout $equipmentLoadout): self
    {
        if ($this->equipmentLoadouts->removeElement($equipmentLoadout)) {
            // set the owning side to null (unless already changed)
            if ($equipmentLoadout->getGovernor() === $this) {
                $equipmentLoadout->setGovernor(null);
            }
        }

        return $this;
    }
}
