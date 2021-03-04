<?php


namespace App\Service\FeatureFlag;


use App\Entity\FeatureFlag;
use App\Repository\FeatureFlagRepository;

class FeatureFlagService
{
    private $repo;

    /** @var FeatureFlag[]|null */
    private $featureFlags = null;

    public function __construct(FeatureFlagRepository $featureFlagRepo)
    {
        $this->repo = $featureFlagRepo;
    }

    public function isActive(string $uid): bool
    {
        if ($this->featureFlags === null) {
            $this->featureFlags = [];
            foreach($this->repo->findAll() as $featureFlag) {
                $this->featureFlags[$featureFlag->getUid()] = $featureFlag;
            }
        }

        return \array_key_exists($uid, $this->featureFlags) ? $this->featureFlags[$uid]->isActive() : false;
    }
}