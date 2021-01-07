<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\GovernorStatus;

class GovernorDetails
{
    public $id;
    public $name;
    public $alliance;
    public $power;
    public $kills;

    public $status;
    public $displayStatus;

    public function __construct(Governor $gov, GovernorSnapshot $mergedSnapshot)
    {
        $this->id = $gov->getGovernorId();
        $this->name = $gov->getName();
        $this->status = $gov->getStatus();
        $this->displayStatus = GovernorStatus::getDisplayStatus($gov->getStatus());
        $this->alliance = $gov->getAlliance();
        $this->power = $mergedSnapshot->getPower();
        $this->kills = $this->sumKills($mergedSnapshot);
    }

    private function sumKills(GovernorSnapshot $snapshot): int
    {
        return $snapshot->getT1Kills() +
            $snapshot->getT2Kills() +
            $snapshot->getT3Kills() +
            $snapshot->getT4Kills() +
            $snapshot->getT5Kills();
    }
}