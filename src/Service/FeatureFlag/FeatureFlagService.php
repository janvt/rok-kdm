<?php


namespace App\Service\FeatureFlag;


use App\Entity\FeatureFlag;
use App\Repository\FeatureFlagRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class FeatureFlagService
{
    private $repo;
    private $authChecker;

    /** @var FeatureFlag[]|null */
    private $featureFlags = null;

    public function __construct(
        FeatureFlagRepository $featureFlagRepo,
        AuthorizationCheckerInterface $authChecker
    )
    {
        $this->repo = $featureFlagRepo;
        $this->authChecker = $authChecker;
    }

    public function isActive(string $uid): bool
    {
        if ($this->featureFlags === null) {
            $this->featureFlags = [];
            foreach($this->repo->findAll() as $featureFlag) {
                $this->featureFlags[$featureFlag->getUid()] = $featureFlag;
            }
        }

        /** @var FeatureFlag|bool $featureFlag */
        $featureFlag = \array_key_exists($uid, $this->featureFlags) ? $this->featureFlags[$uid] : false;

        if (!$featureFlag) {
            return false;
        }

        if ($featureFlag->isActive() && $featureFlag->getRoles()) {
            foreach ($featureFlag->getRoles() as $role) {
                if ($this->authChecker->isGranted($role)) {
                    return true;
                }
            }

            return false;
        }

        return $featureFlag->isActive();
    }
}