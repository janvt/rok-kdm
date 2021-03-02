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

    public $kvkRankings = [];

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

        $this->kills = $mergedSnapshot->getKills();
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

    public function setKvkRankingData(int $kvkNumber, int $ranking, int $contribution)
    {
        $this->kvkRankings[$kvkNumber] = [
            'kvkNumber' => $kvkNumber,
            'ranking' => $ranking,
            'contribution' => $contribution,
        ];
    }
}