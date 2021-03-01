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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GovernorDetailsService
{
    private $govRepo;
    private $govSnapshotRepo;
    private $officerNoteRepo;
    private $snapshotRepo;

    private $authChecker;

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

        $this->setKvk4Ranking($governor, $details);
        $this->setKvk5Ranking($governor, $details);

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

    private function setKvk4Ranking(Governor $governor, GovernorDetails $details)
    {
        $kvk4Snapshot = $this->snapshotRepo->findOneBy(['uid' => Snapshot::UID_KVK4]);
        if (!$kvk4Snapshot) {
            return;
        }

        $govKvk4Snapshot = $this->govSnapshotRepo->findOneBy(['governor' => $governor, 'snapshot' => $kvk4Snapshot]);
        if ($govKvk4Snapshot) {
            $details->setKvk4Data($govKvk4Snapshot->getRank(), $govKvk4Snapshot->getContribution());
        }
    }

    private function setKvk5Ranking(Governor $governor, GovernorDetails $details)
    {
        $kvk5Snapshot = $this->snapshotRepo->findOneBy(['uid' => Snapshot::UID_KVK5]);
        if (!$kvk5Snapshot) {
            return;
        }

        $govKvk5Snapshot = $this->govSnapshotRepo->findOneBy(['governor' => $governor, 'snapshot' => $kvk5Snapshot]);
        if ($govKvk5Snapshot) {
            $details->setKvk5Data($govKvk5Snapshot->getRank(), $govKvk5Snapshot->getContribution());
        }
    }

    private function setKvk6Ranking(Governor $governor, GovernorDetails $details)
    {
        $kvk6Snapshot = $this->snapshotRepo->findOneBy(['uid' => Snapshot::UID_KVK6]);
        if (!$kvk6Snapshot) {
            return;
        }

        $govKvk6Snapshot = $this->govSnapshotRepo->findOneBy(['governor' => $governor, 'snapshot' => $kvk6Snapshot]);
        if ($govKvk6Snapshot) {
            $details->setKvk6Data($govKvk6Snapshot->getRank(), $govKvk6Snapshot->getContribution());
        }
    }
}