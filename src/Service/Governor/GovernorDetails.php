<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\GovernorStatus;

class GovernorDetails
{
    public $user;

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

    public $kvk4Ranking;
    public $kvk4Contribution;
    public $kvk5Ranking;
    public $kvk5Contribution;
    public $kvk6Ranking;
    public $kvk6Contribution;

    public $officerNotes;

    public function __construct(Governor $gov, GovernorSnapshot $mergedSnapshot)
    {
        $this->user = $gov->getUser();

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
    
    public function setOfficerNotes(array $notes)
    {
        $this->officerNotes = $notes;
    }

    public function setKvk4Data(int $ranking, int $contribution)
    {
        $this->kvk4Ranking = $ranking;
        $this->kvk4Contribution = $contribution;
    }

    public function setKvk5Data(int $ranking, int $contribution)
    {
        $this->kvk5Ranking = $ranking;
        $this->kvk5Contribution = $contribution;
    }

    public function setKvk6Data(int $ranking, int $contribution)
    {
        $this->kvk6Ranking = $ranking;
        $this->kvk6Contribution = $contribution;
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