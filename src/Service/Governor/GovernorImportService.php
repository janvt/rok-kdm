<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\GovernorStatus;
use App\Exception\APIException;
use App\Exception\GovDataException;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\SnapshotRepository;

class GovernorImportService
{
    private $govRepo;
    private $govSnapshotRepo;
    private $snapshotRepo;

    public function __construct(
        GovernorRepository $govRepo,
        GovernorSnapshotRepository $govSnapshotRepo,
        SnapshotRepository $snapshotRepo
    )
    {
        $this->govRepo = $govRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
        $this->snapshotRepo = $snapshotRepo;
    }

    /**
     * @param object $data
     * @return Governor
     * @throws APIException
     */
    public function createGovernor(object $data): Governor
    {
        $govId = $this->checkGovId($data);

        $existingGov = $this->govRepo->findBy(['governor_id' => $govId]);
        if ($existingGov) {
            throw new APIException('Gov id already exists: ' . $govId);
        }

        try {
            $status = $this->getField($data, 'status') ?: GovernorStatus::STATUS_UNKNOWN;

            $gov = Governor::createFromId($govId, $status);
            $gov->setName($this->getField($data, 'name'));
        } catch (GovDataException $e) {
            throw new APIException($e->getMessage());
        }

        return $this->govRepo->save($gov);
    }

    /**
     * @param object $data
     * @return GovernorSnapshot
     * @throws APIException
     */
    public function addSnapshot(object $data): GovernorSnapshot
    {
        try {
            $govId = $this->checkGovId($data);

            $gov = null;
            $existingGovs = $this->govRepo->findBy(['governor_id' => $govId]);

            if (count($existingGovs) === 0) {
                throw new APIException('Could not find gov by id: ' . $govId);
            }
        } catch (APIException $e) {
            $govName = $this->getField($data, 'name');
            if ($govName) {
                $existingGovs = $this->govRepo->findBy(['name' => $govName]);
                if (count($existingGovs) === 0) {
                    throw new APIException('Could not find gov by name: ' . $govName);
                }
            } else {
                throw $e;
            }
        }

        $gov = $existingGovs[0];

        $snapshot = null;
        $snapshotUid = $this->getField($data, 'snapshot');
        if ($snapshotUid) {
            $snapshot = $this->snapshotRepo->findOneBy(['uid' => $snapshotUid]);
            if (!$snapshot) {
                throw new APIException('Could not find snapshot: ' . $snapshotUid);
            }
        }

        $created = isset($data->created) ? new \DateTime($data->created) : new \DateTime();

        $govSnapshot = GovernorSnapshot::fromGov($gov, $created);
        $govSnapshot->setAlliance($this->getField($data, 'alliance'));
        $govSnapshot->setKingdom($this->getField($data, 'kingdom'));
        $govSnapshot->setPower($this->getField($data, 'power'));
        $govSnapshot->setHighestPower($this->getField($data, 'highest_power'));
        $govSnapshot->setKills($this->getField($data, 'kills'));
        $govSnapshot->setT1Kills($this->getField($data, 't1_kills'));
        $govSnapshot->setT2Kills($this->getField($data, 't2_kills'));
        $govSnapshot->setT3Kills($this->getField($data, 't3_kills'));
        $govSnapshot->setT4Kills($this->getField($data, 't4_kills'));
        $govSnapshot->setT5Kills($this->getField($data, 't5_kills'));
        $govSnapshot->setDeads($this->getField($data, 'deads'));
        $govSnapshot->setHelps($this->getField($data, 'helps'));
        $govSnapshot->setRssGathered($this->getField($data, 'rss_gathered'));
        $govSnapshot->setRssAssistance($this->getField($data, 'rss_assistance'));
        $govSnapshot->setRank($this->getField($data, 'rank'));
        $govSnapshot->setContribution($this->getField($data, 'contribution'));

        if ($snapshot){
            $govSnapshotForUid = $this->govSnapshotRepo->getGovSnapshotForSnapshot($gov, $snapshot);
            if ($govSnapshotForUid) {
                return $this->govSnapshotRepo->save($govSnapshotForUid->merge($govSnapshot));
            } else {
                $govSnapshot->setSnapshot($snapshot);
            }
        }

        return $this->govSnapshotRepo->save($govSnapshot);
    }

    /**
     * @param object $data
     * @return mixed
     * @throws APIException
     */
    private function checkGovId(object $data)
    {
        $govId = isset($data->id) ? $data->id : null;
        if (!$govId) {
            throw new APIException('Missing gov id!');
        }

        return $govId;
    }

    private function getField(object $data, string $fieldName, $defaultValue = null)
    {
        return isset($data->{$fieldName}) ? $data->{$fieldName} : $defaultValue;
    }
}