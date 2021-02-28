<?php


namespace App\Service\Snapshot;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Snapshot;
use App\Exception\NotFoundException;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\SnapshotRepository;
use App\Repository\SnapshotToGovernorRepository;

class SnapshotService
{
    private $snapshotRepo;
    private $govRepo;
    private $govSnapshotRepo;
    private $snapshotToGovRepo;

    public function __construct(
        SnapshotRepository $snapshotRepo,
        GovernorRepository $govRepo,
        GovernorSnapshotRepository $govSnapshotRepo,
        SnapshotToGovernorRepository $snapshotToGovRepo
    )
    {
        $this->snapshotRepo = $snapshotRepo;
        $this->govRepo = $govRepo;
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
    public function getSnapshotForUid(string $snapshotUid): Snapshot
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

        $total = count($this->snapshotToGovRepo->findBy(['snapshot' => $snapshot]));
        $snapshotInfo->setTotal($total);
        $snapshotInfo->setNumCompleted($total - count($this->getIncompleteGovSnapshots($snapshot)));

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
        $missing = $this->snapshotToGovRepo->getMissingGovForSnapshot($snapshot);
        $govs = [];
        foreach ($missing as $snapshot2Gov) {
            $govs[] = $snapshot2Gov->getGovernor();
        }

        return $govs;
    }

    /**
     * @param int $id
     * @return GovernorSnapshot
     * @throws NotFoundException
     */
    public function getGovSnapshot(int $id): GovernorSnapshot
    {
        $govSnapshot = $this->govSnapshotRepo->find($id);

        if (!$govSnapshot) {
            throw new NotFoundException('governor snapshot', $id);
        }

        return $govSnapshot;
    }

    /**
     * @param int $snapshotId
     * @param int $govId
     * @return GovernorSnapshot
     * @throws NotFoundException
     */
    public function createGovSnapshot(int $snapshotId, int $govId): GovernorSnapshot
    {
        $snapshot = $this->snapshotRepo->find($snapshotId);
        if (!$snapshot) {
            throw new NotFoundException('snapshot', $snapshotId);
        }

        $gov = $this->govRepo->find($govId);
        if (!$gov) {
            throw new NotFoundException('governor', $snapshotId);
        }

        $govSnapshot = new GovernorSnapshot();
        $govSnapshot->setCreated(new \DateTime);
        $govSnapshot->setGovernor($gov);
        $govSnapshot->setSnapshot($snapshot);

        return $this->govSnapshotRepo->save($govSnapshot);
    }

    /**
     * @param GovernorSnapshot $govSnapshot
     * @return GovernorSnapshot
     */
    public function updateGovSnapshot(GovernorSnapshot $govSnapshot): GovernorSnapshot
    {
        return $this->govSnapshotRepo->save($govSnapshot);
    }
}