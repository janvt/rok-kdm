<?php


namespace App\Service\Snapshot;


use App\Entity\Alliance;
use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Snapshot;
use App\Entity\SnapshotToGovernor;

class SnapshotInfo
{
    private $snapshot;
    /** @var Alliance[] */
    private $alliances = [];
    /** @var SnapshotToGovernor[] */
    private $snapshotToGovs = [];
    /** @var GovernorSnapshot[]  */
    private $complete = [];
    /** @var GovernorSnapshot[]  */
    private $incomplete = [];
    /** @var Governor[] */
    private $missing = [];
    /** @var int|null  */
    private $allianceFilter;

    /**
     * @param Snapshot $snapshot
     * @param SnapshotToGovernor[] $snapshotToGovs
     * @param GovernorSnapshot[] $govSnapshots
     * @param Governor[] $missing
     * @param ?int $allianceFilter
     */
    public function __construct(
        Snapshot $snapshot,
        array $snapshotToGovs,
        array $govSnapshots,
        array $missing,
        int $allianceFilter = null
    )
    {
        $this->snapshot = $snapshot;
        $this->allianceFilter = $allianceFilter;

        // compute unique alliances and then filter snapshot to govs
        foreach ($snapshotToGovs as $snapshotToGov) {
            $alliance = $snapshotToGov->getGovernor()->getAlliance();
            if ($alliance) {
                $this->alliances[$alliance->getTag()] = $alliance;
            }

            if ($this->allianceAllowedByFilter($alliance)) {
                $this->snapshotToGovs[] = $snapshotToGovs;
            }
        }

        // filter missing govs
        foreach ($missing as $missingGov) {
            if ($this->allianceAllowedByFilter($missingGov->getAlliance())) {
                $this->missing[] = $missingGov;
            }
        }

        foreach($govSnapshots as $govSnapshot) {
            if ($this->allianceAllowedByFilter($govSnapshot->getGovernor()->getAlliance())) {
                if ($govSnapshot->getCompleted()) {
                    $this->complete[] = $govSnapshot;
                } else {
                    $this->incomplete[] = $govSnapshot;
                }
            }
        }
    }

    public function getName(): string
    {
        return $this->snapshot->getName();
    }

    public function getStatus(): string
    {
        return $this->snapshot->getStatus();
    }

    public function getTotal(): int
    {
        return count($this->snapshotToGovs);
    }

    public function getNumCompleted(): int
    {
        return count($this->complete);
    }

    public function getNumIncomplete(): int
    {
        return count($this->incomplete);
    }

    public function getNumMissing(): int
    {
        return count($this->missing);
    }

    public function getCompleted(): ?\DateTimeInterface
    {
        return $this->snapshot->getCompleted();
    }

    public function isActive(): bool
    {
        return $this->snapshot->isActive();
    }

    public function getUid(): string
    {
        return $this->snapshot->getUid();
    }

    public function getDateCompleted(): ?\DateTimeInterface
    {
        return $this->snapshot->getCompleted();
    }

    /**
     * @return Alliance[]
     */
    public function getAlliances(): array
    {
        return $this->alliances;
    }

    /**
     * @return GovernorSnapshot[]
     */
    public function getCompletedGovSnapshots(): array
    {
        return $this->complete;
    }

    /**
     * @return GovernorSnapshot[]
     */
    public function getIncompleteGovSnapshots(): array
    {
        return $this->incomplete;
    }

    /**
     * @return Governor[]
     */
    public function getMissingGovs(): array
    {
        return $this->missing;
    }

    private function allianceAllowedByFilter(?Alliance $alliance): bool
    {
        if (!$this->allianceFilter) {
            return true;
        }

        if (!$alliance) {
            return false;
        }

        return $alliance->getId() === $this->allianceFilter;
    }
}