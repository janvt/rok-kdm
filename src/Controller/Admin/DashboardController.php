<?php

namespace App\Controller\Admin;

use App\Entity\Alliance;
use App\Entity\Commander;
use App\Entity\Equipment;
use App\Entity\EquipmentInventory;
use App\Entity\EquipmentLoadout;
use App\Entity\FeatureFlag;
use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\OfficerNote;
use App\Entity\Role;
use App\Entity\Snapshot;
use App\Entity\SnapshotToGovernor;
use App\Entity\User;
use App\Service\FeatureFlag\FeatureFlagService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $featureFlagService;

    public function __construct(FeatureFlagService $featureFlagService)
    {
        $this->featureFlagService = $featureFlagService;
    }

    /**
     * @Route("/admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect(
            $routeBuilder->setController(GovernorCrudController::class)->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->getParameter('site_title') . ' Admin')
            ->disableUrlSignatures()
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Main Site', 'fa fa-home', '/');

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fas fa-folder-open', User::class);
        yield MenuItem::linkToCrud('Governors', 'fas fa-folder-open', Governor::class);
        yield MenuItem::linkToCrud('Alliances', 'fas fa-folder-open', Alliance::class);

        $ffCommanders = $this->featureFlagService->isActive(FeatureFlag::COMMANDERS);
        $ffEquipment = $this->featureFlagService->isActive(FeatureFlag::EQUIPMENT);
        if ($ffCommanders || $ffEquipment) {
            yield MenuItem::section('Toys');
            if ($ffCommanders) {
                yield MenuItem::linkToCrud('Commanders', 'fas fa-folder-open', Commander::class);
            }

            if ($ffEquipment) {
                yield MenuItem::linkToCrud('Equipment Inventory', 'fas fa-folder-open', EquipmentInventory::class);
                yield MenuItem::linkToCrud('Equipment Loadouts', 'fas fa-folder-open', EquipmentLoadout::class);
            }
        }

        yield MenuItem::section('Officer Admin');
        yield MenuItem::linkToCrud('Notes', 'fas fa-folder-open', OfficerNote::class);

        if ($this->isGranted(Role::ROLE_SCRIBE_ADMIN)) {
            yield MenuItem::section('Scribe Admin');
            yield MenuItem::linkToCrud('Snapshots', 'fas fa-folder-open', Snapshot::class);
            yield MenuItem::linkToCrud('Gov Snapshots', 'fas fa-folder-open', GovernorSnapshot::class);
            yield MenuItem::linkToCrud('Snapshot Mappings', 'fas fa-folder-open', SnapshotToGovernor::class);
        }

        if ($this->isGranted(Role::ROLE_SUPERADMIN)) {
            yield MenuItem::section('Site Admin');
            yield MenuItem::linkToCrud('Feature Flags', 'fas fa-folder-open', FeatureFlag::class);
        }
    }
}
