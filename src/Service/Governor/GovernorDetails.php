<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorSnapshot;

class GovernorDetails
{
    public $name;
    public $power;
    public $kills;

    public function __construct(Governor $gov, GovernorSnapshot $mergedSnapshot)
    {
        dump($mergedSnapshot);
        $this->name = $gov->getName();
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