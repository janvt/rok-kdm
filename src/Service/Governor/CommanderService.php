<?php


namespace App\Service\Governor;


use App\Entity\Commander;
use App\Entity\Governor;
use App\Repository\CommanderRepository;
use App\Repository\GovernorRepository;

class CommanderService
{
    private $repo;
    private $govRepo;

    public function __construct(
        CommanderRepository $commanderRepo,
        GovernorRepository $govRepo
    )
    {
        $this->repo = $commanderRepo;
        $this->govRepo = $govRepo;
    }

    /**
     * @param Governor $gov
     */
    public function ensureAllCommanders(Governor $gov): Governor
    {
        $all = $this->repo->loadAllForGov($gov);
        foreach(CommanderNames::ALL as $uid => $name) {
            if (\array_key_exists($uid, $all)) {
                continue;
            }

            $commander = new Commander();
            $commander->setUid($uid);
            $commander->setGovernor($gov);

            $this->repo->save($commander);
        }

        return $this->govRepo->save($gov);
    }

    /**
     * @param Governor $gov
     * @return Commander[]
     */
    public function getAllForGov(Governor $gov): array
    {
        return $this->repo->loadAllForGov($gov);
    }

    public function save(Commander $commander): Commander
    {
        return $this->repo->save($commander);
    }
}