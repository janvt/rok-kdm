<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\GovernorRepository;
use App\Repository\GovernorSnapshotRepository;
use App\Repository\OfficerNoteRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GovernorDetailsService
{
    private $govRepo;
    private $govSnapshotRepo;
    private $authChecker;
    private $officerNoteRepo;

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
        AuthorizationCheckerInterface $authChecker,
        GovernorSnapshotRepository $govSnapshotRepo,
        OfficerNoteRepository $officerNoteRepo
    ) {
        $this->govRepo = $govRepo;
        $this->authChecker = $authChecker;
        $this->govSnapshotRepo = $govSnapshotRepo;
        $this->officerNoteRepo = $officerNoteRepo;
    }

    /**
     * @param Governor $governor
     * @param User|UserInterface $user
     * @return GovernorDetails
     */
    public function createGovernorDetails(Governor $governor, User $user): GovernorDetails
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

        if ($this->authChecker->isGranted(Role::ROLE_OFFICER, $user)) {
            $details->setOfficerNotes(
                $this->officerNoteRepo->findBy(['governor' => $governor])
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
}