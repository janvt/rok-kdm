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
    public $highestPower;
    public $deads;
    public $helps;
    public $rssAssistance;

    public $kills;
    public $t1kills;
    public $t2kills;
    public $t3kills;
    public $t4kills;
    public $t5kills;

    public $status;
    public $displayStatus;

    public function __construct(Governor $gov, GovernorSnapshot $mergedSnapshot)
    {
        $this->id = $gov->getGovernorId();
        $this->name = $gov->getName();
        $this->alliance = $gov->getAlliance();

        $this->status = $gov->getStatus();
        $this->displayStatus = GovernorStatus::getDisplayStatus($gov->getStatus());

        $this->power = $mergedSnapshot->getPower();
        $this->highestPower = $mergedSnapshot->getHighestPower();
        $this->deads = $mergedSnapshot->getDeads();
        $this->helps = $mergedSnapshot->getHelps();
        $this->rssAssistance = $mergedSnapshot->getRssAssistance();

        $this->kills = $this->sumKills($mergedSnapshot);
        $this->t1kills = $mergedSnapshot->getT1Kills();
        $this->t2kills = $mergedSnapshot->getT2Kills();
        $this->t3kills = $mergedSnapshot->getT3Kills();
        $this->t4kills = $mergedSnapshot->getT4Kills();
        $this->t5kills = $mergedSnapshot->getT5Kills();
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