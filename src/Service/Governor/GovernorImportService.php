<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\GovernorStatus;
use App\Exception\APIException;
use App\Exception\GovDataException;
use App\Exception\ImportException;
use App\Repository\AllianceRepository;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\SnapshotRepository;

class GovernorImportService
{
    private $govRepo;
    private $allianceRepo;
    private $govSnapshotRepo;
    private $snapshotRepo;

    public function __construct(
        GovernorRepository $govRepo,
        AllianceRepository $allianceRepo,
        GovernorSnapshotRepository $govSnapshotRepo,
        SnapshotRepository $snapshotRepo
    )
    {
        $this->govRepo = $govRepo;
        $this->allianceRepo = $allianceRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
        $this->snapshotRepo = $snapshotRepo;
    }

    /**
     * @param string $csvData
     * @throws ImportException
     */
    public function processCSV(string $csvData)
    {
        try {
            $csv = array_map('str_getcsv', explode("\n", $csvData));
        } catch (\Exception $e) {
            throw new ImportException('Could not parse CSV: ' . $e->getMessage());
        }

        dump($csv);
    }

    /**
     * @param object $data
     * @return Governor
     * @throws APIException
     */
    public function createOrUpdateGovernor(object $data): Governor
    {
        try {
            $govId = $this->checkGovId($data);

            $gov = $this->govRepo->findOneBy(['governor_id' => $govId]);
            if (!$gov) {
                $gov = Governor::createFromId($govId);
            }

            $status = $this->getField($data, 'status') ?: GovernorStatus::STATUS_UNKNOWN;
            $gov->setStatus($status);
            $gov->setName($this->getField($data, 'name'));

            $alliance = $this->allianceRepo->findOneBy(['tag' => $data->alliance]);
            if ($alliance) {
                $gov->setAlliance($alliance);
            }

            return $this->govRepo->save($gov);
        } catch (GovDataException $e) {
            throw new APIException($e->getMessage());
        }
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