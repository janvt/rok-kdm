<?php

namespace App\Controller\Admin;

use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\OfficerNote;
use App\Entity\Role;
use App\Entity\Snapshot;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lugal Admin');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(100);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('User', 'fas fa-folder-open', User::class);
        yield MenuItem::linkToCrud('Governor', 'fas fa-folder-open', Governor::class);

        if ($this->isGranted(Role::ROLE_OFFICER)) {
            yield MenuItem::linkToCrud('Notes', 'fas fa-folder-open', OfficerNote::class);
        }

        yield MenuItem::linkToCrud('Snapshots', 'fas fa-folder-open', Snapshot::class);
        yield MenuItem::linkToCrud('Gov Snapshots', 'fas fa-folder-open', GovernorSnapshot::class);
    }
}
