<?php


namespace App\Service\Snapshot;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Snapshot;
use App\Exception\NotFoundException;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\SnapshotRepository;
use App\Repository\SnapshotToGovernorRepository;

class SnapshotService
{
    private $snapshotRepo;
    private $govSnapshotRepo;
    private $snapshotToGovRepo;

    public function __construct(
        SnapshotRepository $snapshotRepo,
        GovernorSnapshotRepository $govSnapshotRepo,
        SnapshotToGovernorRepository $snapshotToGovRepo
    )
    {
        $this->snapshotRepo = $snapshotRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
        $this->snapshotToGovRepo = $snapshotToGovRepo;
    }

    /**
     * @return SnapshotInfo[]
     */
    public function getSnapshotsInfo(): array
    {
        $snapshots = $this->snapshotRepo->findBy([], ['created' => 'DESC'], 10);
        $snapshotInfos = [];

        foreach ($snapshots as $snapshot) {
            $snapshotInfos[] = $this->createSnapshotInfo($snapshot);
        }

        return $snapshotInfos;
    }

    /**
     * @param string $snapshotUid
     * @return Snapshot
     * @throws NotFoundException
     */
    public function getSnapshotForUuid(string $snapshotUid): Snapshot
    {
        $snapshot = $this->snapshotRepo->findOneBy(['uid' => $snapshotUid]);

        if (!$snapshot) {
            throw new NotFoundException('snapshot', $snapshotUid);
        }

        return $snapshot;
    }

    public function createSnapshotInfo(Snapshot $snapshot): SnapshotInfo
    {
        $snapshotInfo = new SnapshotInfo($snapshot);

        $snapshotToGovs = $this->snapshotToGovRepo->findBy(['snapshot' => $snapshot]);
        $govSnapshots = $this->govSnapshotRepo->findBy(['snapshot' => $snapshot]);

        $snapshotInfo->setTotal(count($snapshotToGovs));
        $snapshotInfo->setNumCompleted(count($govSnapshots));

        return $snapshotInfo;
    }

    /**
     * @param Snapshot $snapshot
     * @return GovernorSnapshot[]
     */
    public function getIncompleteGovSnapshots(Snapshot $snapshot): array
    {
        return $this->govSnapshotRepo->getIncompleteForSnapshot($snapshot);
    }

    /**
     * @param Snapshot $snapshot
     * @return Governor[]
     */
    public function getMissingGovs(Snapshot $snapshot): array
    {
        return $this->snapshotToGovRepo->getMissingGovs($snapshot);
    }
}