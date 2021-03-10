<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Role;
use App\Entity\Snapshot;
use App\Entity\User;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\OfficerNoteRepository;
use App\Repository\SnapshotRepository;
use App\Service\Snapshot\SnapshotService;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GovernorDetailsService
{
    private $govRepo;
    private $govSnapshotRepo;
    private $officerNoteRepo;
    private $snapshotService;

    private $authChecker;

    const FIELDS = [
        'Power',
        'HighestPower',
        'Kills',
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

    public function __construct(
        GovernorRepository $govRepo,
        GovernorSnapshotRepository $govSnapshotRepo,
        OfficerNoteRepository $officerNoteRepo,
        SnapshotService $snapshotService,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->govRepo = $govRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
        $this->officerNoteRepo = $officerNoteRepo;
        $this->snapshotService = $snapshotService;

        $this->authChecker = $authChecker;
    }

    /**
     *
     * @param User|UserInterface $user
     * @return GovernorDetails[]
     */
    public function getFeaturedGovs(User $user): array
    {
        $featuredGovs = [
            'title' => 'Your Governors',
            'govs' => []
        ];

        $govs = $this->govRepo->findBy(['user' => $user]);
        foreach ($govs as $gov) {
            $featuredGovs['govs'][] = $this->createGovernorDetails($gov);
        }

        return $featuredGovs;
    }

    /**
     * @param Governor $governor
     * @param bool $computeKvkRankingData
     * @param User|UserInterface|null $user
     * @param Snapshot|null $snapshot
     * @return GovernorDetails
     */
    public function createGovernorDetails(
        Governor $governor,
        bool $computeKvkRankingData = true,
        ?User $user = null,
        ?Snapshot $snapshot = null
    ): GovernorDetails
    {
        $snapshotCriteria = ['governor' => $governor];
        if ($snapshot) {
            $snapshotCriteria['snapshot'] = $snapshot;
        }

        $snapshots = $this->govSnapshotRepo->findBy($snapshotCriteria, ['created' => 'DESC']);
        $mergedSnapshot = new GovernorSnapshot();
        foreach (self::FIELDS as $field) {
            $latestValue = $this->searchFor($field, $snapshots);
            if ($latestValue) {
                $mergedSnapshot->{'set' . $field}($latestValue);
            }
        }

        $details = new GovernorDetails($governor, $mergedSnapshot);

        if ($computeKvkRankingData) {
            foreach (SnapshotService::KVK_NUMBERS as $kvkNumber) {
                $this->setKvkRanking($kvkNumber, $governor, $details);
            }
        }

        if ($user && $this->authChecker->isGranted(Role::ROLE_OFFICER, $user)) {
            $details->setOfficerNotes(
                $this->officerNoteRepo->findBy(['governor' => $governor], ['created' => 'DESC'])
            );
        }

        return $details;
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

    private function setKvkRanking(int $kvkNumber, Governor $governor, GovernorDetails $details)
    {
        $kvkSnapshots = $this->snapshotService->getKVKSnapshots();
        if (!isset($kvkSnapshots[$kvkNumber])) {
            return;
        }

        $govKvkSnapshot = $this->govSnapshotRepo->findOneBy([
            'governor' => $governor,
            'snapshot' => $kvkSnapshots[$kvkNumber]
        ]);

        if ($govKvkSnapshot) {
            $details->setKvkRankingData($kvkNumber, $govKvkSnapshot->getRank(), $govKvkSnapshot->getContribution());
        }
    }
}