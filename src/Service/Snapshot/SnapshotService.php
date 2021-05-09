<?php


namespace App\Service\Snapshot;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Snapshot;
use App\Entity\SnapshotToGovernor;
use App\Exception\NotFoundException;
use App\Exception\SnapshotDataException;
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

    private $kvkSnapshots = null;
    const KVK_NUMBERS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

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

    /**
     * @param bool $includeAll
     * @return SnapshotInfo[]
     */
    public function getSnapshotsInfo(bool $includeAll = false): array
    {
        $criteria = [];
        if (!$includeAll) {
            $criteria['status'] = Snapshot::STATUS_ACTIVE;
        }

        $snapshots = $this->snapshotRepo->findBy($criteria, ['status' => 'ASC', 'created' => 'DESC'], 10);
        $snapshotInfos = [];

        foreach ($snapshots as $snapshot) {
            $snapshotInfos[] = $this->createSnapshotInfo($snapshot);
        }

        return $snapshotInfos;
    }

    public function createSnapshotInfo(Snapshot $snapshot, ?int $allianceFilter = null): SnapshotInfo
    {
        return new SnapshotInfo(
            $snapshot,
            $this->snapshotToGovRepo->findBy(['snapshot' => $snapshot]),
            $this->govSnapshotRepo->findBy(['snapshot' => $snapshot]),
            $this->getMissingGovs($snapshot),
            $allianceFilter
        );
    }

    public function populateSnapshot(Snapshot $snapshot)
    {
        $govs = $this->govRepo->getGovernorsFromMainAlliances();

        foreach ($govs as $gov) {
            if ($this->snapshotToGovRepo->findOneBy(['governor' => $gov, 'snapshot' => $snapshot])) {
                continue;
            }

            $snapshotToGov = new SnapshotToGovernor();
            $snapshotToGov->setGovernor($gov);
            $snapshotToGov->setSnapshot($snapshot);
            $snapshotToGov->setCreated(new \DateTime);

            $this->snapshotToGovRepo->save($snapshotToGov);
        }
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

    /**
     * @param Snapshot $snapshot
     * @return Snapshot
     * @throws SnapshotDataException
     */
    public function createSnapshot(Snapshot $snapshot): Snapshot
    {
        if ($this->snapshotRepo->findOneBy(['uid' => $snapshot->getUid()])) {
            throw new SnapshotDataException('Uid already exists!');
        }

        return $this->snapshotRepo->save($snapshot);
    }

    public function markActive(Snapshot $snapshot): Snapshot
    {
        $snapshot->setStatus(Snapshot::STATUS_ACTIVE);
        return $this->snapshotRepo->save($snapshot);
    }

    public function markCompleted(Snapshot $snapshot): Snapshot
    {
        $snapshot->setCompleted(new \DateTime);
        return $this->snapshotRepo->save($snapshot);
    }

    public function markGovSnapshotsCompleted(Snapshot $snapshot, ?int $alliance = null)
    {
        $govSnapshots = $this->govSnapshotRepo->getGovSnapshotsForSnapshot($snapshot, true, $alliance);
        foreach($govSnapshots as $govSnapshot) {
            $govSnapshot->setCompleted(new \DateTime);
            $this->govSnapshotRepo->save($govSnapshot);
        }
    }

    /**
     * @return Snapshot[]
     */
    public function getKVKSnapshots(): array
    {
        if ($this->kvkSnapshots === null) {
            $kvkUids = [];
            foreach (self::KVK_NUMBERS as $kvkNumber) {
                $kvkUids[] = 'kvk' . $kvkNumber;
            }

            $this->kvkSnapshots = $this->snapshotRepo->loadByUid($kvkUids);
        }

        return $this->kvkSnapshots;
    }
}