<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\OfficerNoteRepository;
use App\Repository\SnapshotRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GovernorDetailsService
{
    private $govRepo;
    private $govSnapshotRepo;
    private $officerNoteRepo;
    private $snapshotRepo;

    private $authChecker;

    const KVK_NUMBERS = [1,2,3,4,5,6,7,8,9,10];

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

    public function __construct(
        GovernorRepository $govRepo,
        GovernorSnapshotRepository $govSnapshotRepo,
        OfficerNoteRepository $officerNoteRepo,
        SnapshotRepository $snapshotRepo,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->govRepo = $govRepo;
        $this->govSnapshotRepo = $govSnapshotRepo;
        $this->officerNoteRepo = $officerNoteRepo;
        $this->snapshotRepo = $snapshotRepo;

        $this->authChecker = $authChecker;
    }

    /**
     * @return GovernorDetails[]
     */
    public function getFeaturedGovs(): array
    {
        $featuredGovs = [];
        $govs = $this->govRepo->getFeatured();
        foreach ($govs as $gov) {
            $featuredGovs[] = $this->createGovernorDetails($gov);
        }

        return $featuredGovs;
    }

    /**
     * @param Governor $governor
     * @param User|UserInterface|null $user
     * @return GovernorDetails
     */
    public function createGovernorDetails(Governor $governor, ?User $user = null): GovernorDetails
    {
        $snapshots = $this->govSnapshotRepo->findBy(['governor' => $governor], ['created' => 'DESC']);
        $mergedSnapshot = new GovernorSnapshot();
        foreach (self::FIELDS as $field) {
            $latestValue = $this->searchFor($field, $snapshots);
            if ($latestValue) {
                $mergedSnapshot->{'set' . $field}($latestValue);
            }
        }

        $details = new GovernorDetails($governor, $mergedSnapshot);

        foreach (self::KVK_NUMBERS as $kvkNumber) {
            $this->setKvkRanking($kvkNumber, $governor, $details);
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
        $kvkSnapshot = $this->snapshotRepo->findOneBy(['uid' => 'kvk' . $kvkNumber]);
        if (!$kvkSnapshot) {
            return;
        }

        $govKvkSnapshot = $this->govSnapshotRepo->findOneBy(['governor' => $governor, 'snapshot' => $kvkSnapshot]);
        if ($govKvkSnapshot) {
            $details->setKvkRankingData($kvkNumber, $govKvkSnapshot->getRank(), $govKvkSnapshot->getContribution());
        }
    }
}