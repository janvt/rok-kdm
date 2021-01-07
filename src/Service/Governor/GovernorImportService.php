<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\GovernorStatus;
use App\Exception\APIException;
use App\Exception\GovDataException;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;

class GovernorImportService
{
    private $govRepo;
    private $govSnapshotRepo;

    public function __construct(
        GovernorRepository $govRepo,
        GovernorSnapshotRepository $govSnapshotRepo
    )
    {
        $this->govRepo = $govRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
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
            $gov->setAlliance($this->getField($data, 'alliance'));
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
        $govId = $this->checkGovId($data);

        $existingGovs = $this->govRepo->findBy(['governor_id' => $govId]);
        if (count($existingGovs) !== 1) {
            throw new APIException('Could not find gov: ' . $govId);
        }

        $created = isset($data->created) ? new \DateTime($data->created) : new \DateTime();

        $snapshot = GovernorSnapshot::fromGov($existingGovs[0], $created);
        $snapshot->setAlliance($this->getField($data, 'alliance'));
        $snapshot->setKingdom($this->getField($data, 'kingdom'));
        $snapshot->setPower($this->getField($data, 'power'));
        $snapshot->setHighestPower($this->getField($data, 'highest_power'));
        $snapshot->setT1Kills($this->getField($data, 't1_kills'));
        $snapshot->setT2Kills($this->getField($data, 't2_kills'));
        $snapshot->setT3Kills($this->getField($data, 't3_kills'));
        $snapshot->setT4Kills($this->getField($data, 't4_kills'));
        $snapshot->setT5Kills($this->getField($data, 't5_kills'));
        $snapshot->setDeads($this->getField($data, 'deads'));
        $snapshot->setHelps($this->getField($data, 'helps'));
        $snapshot->setRssGathered($this->getField($data, 'rss_gathered'));
        $snapshot->setRssAssistance($this->getField($data, 'rss_assistance'));

        return $this->govSnapshotRepo->save($snapshot);
    }

    /**
     * @param object $data
     * @return mixed
     * @throws APIException
     */
    private function checkGovId(object $data)
    {
        $govId = $data->id;
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