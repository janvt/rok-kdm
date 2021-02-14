<?php

namespace App\Controller\Admin;

use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Role;
use App\Entity\Snapshot;
use App\Entity\SnapshotToGovernor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class SnapshotToGovernorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SnapshotToGovernor::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Snapshot to Governor')
            ->setEntityLabelInPlural('Governors')
            ->setSearchFields(['governor.name', 'snapshot.name', 'snapshot.uid'])
            ->setEntityPermission(Role::ROLE_SCRIBE_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        $governor = AssociationField::new('governor')
            ->setFieldFqcn(Governor::class);
        $snapshot = AssociationField::new('snapshot')
            ->setFieldFqcn(Snapshot::class);
        $created = DateTimeField::new('created');
        $updated = DateTimeField::new('updated');
        $completed = DateTimeField::new('completed');

        return [
            $governor,
            $snapshot,
            $created,
            $updated,
            $completed,
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $snapshotToGov = new SnapshotToGovernor();
        $snapshotToGov->setCreated(new \DateTime);

        return $snapshotToGov;
    }
}
