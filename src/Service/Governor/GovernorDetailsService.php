<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;

class GovernorDetailsService
{
    private $govRepo;
    private $govSnapshotRepo;

    const FIELDS = [
        'Power',
        'HighestPower',
        'T1Kills',
        'T2Kills',
        'T3Kills',
        'T4Kills',
        'T5Kills',
        'Deads',
        'Helps',
        'RSSGathered',
        'RSSAssistance',
    ];

    public function __construct(GovernorRepository $govRepo, GovernorSnapshotRepository $govSnapshotRepo)
    {
        $this->govRepo = $govRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
    }

    public function createGovernorDetails(Governor $governor): GovernorDetails
    {
        $snapshots = $this->govSnapshotRepo->findBy(['governor' => $governor], ['created' => 'DESC']);
        $mergedSnapshot = new GovernorSnapshot();
        foreach (self::FIELDS as $field) {
            $latestValue = $this->searchFor($field, $snapshots);
            if ($latestValue) {
                $mergedSnapshot->{'set' . $field}($latestValue);
            }
        }

        return new GovernorDetails($governor, $mergedSnapshot);
    }

    /**
     * @param string $field
     * @param GovernorSnapshot[] $snapshots
     * @return null
     */
    private function searchFor(string $field, array $snapshots)
    {
        foreach ($snapshots as $snapshot) {
            $value = $snapshot->{'get' . $field}();
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}