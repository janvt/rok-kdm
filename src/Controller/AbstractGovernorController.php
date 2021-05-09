<?php

namespace App\Controller;

use App\Entity\Governor;
use App\Entity\Role;
use App\Service\FeatureFlag\FeatureFlagService;
use App\Service\Governor\CommanderService;
use App\Service\Governor\EquipmentService;
use App\Service\Governor\GovernorDetailsService;
use App\Service\Governor\GovernorManagementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractGovernorController extends AbstractController
{
    protected $govManagementService;
    protected $detailsService;
    protected $commanderService;
    protected $equipmentService;
    protected $featureFlagService;

    public function __construct(
        GovernorManagementService $governorManagementService,
        GovernorDetailsService $detailsService,
        CommanderService $commanderService,
        EquipmentService $equipmentService,
        FeatureFlagService $featureFlagService
    )
    {
        $this->govManagementService = $governorManagementService;
        $this->detailsService = $detailsService;
        $this->commanderService = $commanderService;
        $this->equipmentService = $equipmentService;
        $this->featureFlagService = $featureFlagService;
    }

    protected function canEditProfile(Governor $gov): bool
    {
        return $this->userOwnsGov($gov) || $this->isGranted(Role::ROLE_SCRIBE);
    }

    protected function userOwnsGov(Governor $gov): bool
    {
        return $gov->getUser() && $gov->getUser()->getId() === $this->getUser()->getId();
    }
}
