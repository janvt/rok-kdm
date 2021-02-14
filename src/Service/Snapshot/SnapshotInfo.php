<?php


namespace App\Service\Snapshot;


use App\Entity\Snapshot;

class SnapshotInfo
{
    private $snapshot;

    private $total = 0;
    private $numCompleted = 0;

    public function __construct(Snapshot $snapshot)
    {
        $this->snapshot = $snapshot;
    }

    public function getName(): string
    {
        return $this->snapshot->getName();
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): SnapshotInfo
    {
        $this->total = $total;
        return $this;
    }

    public function getNumCompleted(): int
    {
        return $this->numCompleted;
    }

    public function setNumCompleted(int $numCompleted): SnapshotInfo
    {
        $this->numCompleted = $numCompleted;
        return $this;
    }

    public function getCompleted(): ?\DateTimeInterface
    {
        return $this->snapshot->getCompleted();
    }

    public function getUid(): string
    {
        return $this->snapshot->getUid();
    }
}