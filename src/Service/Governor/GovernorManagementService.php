<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\OfficerNote;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Repository\GovernorRepository;
use App\Repository\OfficerNoteRepository;

class GovernorManagementService
{
    private $govRepo;
    private $noteRepo;

    public function __construct(
        GovernorRepository $govRepo,
        OfficerNoteRepository $noteRepo
    )
    {
        $this->govRepo = $govRepo;
        $this->noteRepo = $noteRepo;
    }

    /**
     * @param $id
     * @return Governor
     * @throws NotFoundException
     */
    public function findGov($id): Governor
    {
        $govs = $this->govRepo->findBy(['governor_id' => $id]);
        if (count($govs) !== 1) {
            throw new NotFoundException();
        }

        return $govs[0];
    }

    public function addOfficerNote(Governor $gov, OfficerNote $note, User $user): OfficerNote
    {
        $note->setUser($user);
        $note->setGovernor($gov);
        $note->setCreated(new \DateTime());

        return $this->noteRepo->save($note);
    }
}