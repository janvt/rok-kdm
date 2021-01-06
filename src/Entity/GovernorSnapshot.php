<?php

namespace App\Entity;

use App\Repository\GovernorSnapshotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=GovernorSnapshotRepository::class)
 */
class GovernorSnapshot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Governor::class, inversedBy="snapshots")
     * @ORM\JoinColumn(nullable=false)
     * @Ignore()
     */
    private $governor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $kingdom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $power;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $highest_power;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t1_kills;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t2_kills;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t3_kills;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t4_kills;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t5_kills;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deads;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rss_gathered;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rss_assistance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $helps;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alliance;

    public static function fromGov(Governor $governor, \DateTime $created): GovernorSnapshot
    {
        $snapshot = new self();
        $snapshot->setGovernor($governor);
        $snapshot->setCreated($created);

        return $snapshot;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGovernor(): ?Governor
    {
        return $this->governor;
    }

    public function setGovernor(Governor $governor): GovernorSnapshot
    {
        $this->governor = $governor;

        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): GovernorSnapshot
    {
        $this->created = $created;

        return $this;
    }

    public function getKingdom(): ?string
    {
        return $this->kingdom;
    }

    public function setKingdom(?string $kingdom): self
    {
        $this->kingdom = $kingdom;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getHighestPower(): ?int
    {
        return $this->highest_power;
    }

    public function setHighestPower(?int $highest_power): self
    {
        $this->highest_power = $highest_power;

        return $this;
    }

    public function getT1Kills(): ?int
    {
        return $this->t1_kills;
    }

    public function setT1Kills(?int $t1_kills): self
    {
        $this->t1_kills = $t1_kills;

        return $this;
    }

    public function getT2Kills(): ?int
    {
        return $this->t2_kills;
    }

    public function setT2Kills(?int $t2_kills): self
    {
        $this->t2_kills = $t2_kills;

        return $this;
    }

    public function getT3Kills(): ?int
    {
        return $this->t3_kills;
    }

    public function setT3Kills(?int $t3_kills): self
    {
        $this->t3_kills = $t3_kills;

        return $this;
    }

    public function getT4Kills(): ?int
    {
        return $this->t4_kills;
    }

    public function setT4Kills(?int $t4_kills): self
    {
        $this->t4_kills = $t4_kills;

        return $this;
    }

    public function getT5Kills(): ?int
    {
        return $this->t5_kills;
    }

    public function setT5Kills(?int $t5_kills): self
    {
        $this->t5_kills = $t5_kills;

        return $this;
    }

    public function getDeads(): ?int
    {
        return $this->deads;
    }

    public function setDeads(?int $deads): self
    {
        $this->deads = $deads;

        return $this;
    }

    public function getRssGathered(): ?int
    {
        return $this->rss_gathered;
    }

    public function setRssGathered(?int $rss_gathered): self
    {
        $this->rss_gathered = $rss_gathered;

        return $this;
    }

    public function getRssAssistance(): ?int
    {
        return $this->rss_assistance;
    }

    public function setRssAssistance(?int $rss_assistance): self
    {
        $this->rss_assistance = $rss_assistance;

        return $this;
    }

    public function getHelps(): ?int
    {
        return $this->helps;
    }

    public function setHelps(?int $helps): self
    {
        $this->helps = $helps;

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
