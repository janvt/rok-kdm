<?php

namespace App\Controller\Admin;

use App\Entity\Alliance;
use App\Entity\Governor;
use App\Entity\GovernorSnapshot;
use App\Entity\Role;
use App\Entity\Snapshot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GovernorSnapshotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GovernorSnapshot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Governor Snapshot')
            ->setEntityLabelInPlural('Governor Snapshots')
            ->setSearchFields([
                'governor.name',
                'snapshot.name',
                'snapshot.uid',
            ])
            ->setEntityPermission(Role::ROLE_SCRIBE)
            ->setDefaultSort(['created' => 'DESC'])
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $governor = AssociationField::new('governor');
        $created = DateTimeField::new('created');
        $snapshot = AssociationField::new('snapshot');
        $alliance = TextField::new('alliance');
        $kingdom = TextField::new('kingdom');
        $power = IntegerField::new('power');
        $highestPower = IntegerField::new('highest_power');
        $vip = IntegerField::new('vip');
        $kills = IntegerField::new('kills');
        $deads = IntegerField::new('deads');
        $rank = IntegerField::new('rank');
        $contribution = IntegerField::new('contribution');
        $t1kills = IntegerField::new('t1_kills');
        $t2kills = IntegerField::new('t2_kills');
        $t3kills = IntegerField::new('t3_kills');
        $t4kills = IntegerField::new('t4_kills');
        $t5kills = IntegerField::new('t5_kills');
        $helps = IntegerField::new('helps');
        $rssGathered = IntegerField::new('rss_gathered');
        $rssAssistance = IntegerField::new('rss_assistance');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$governor, $created, $snapshot, $power, $vip, $kills, $t4kills, $t5kills, $deads];
        }

        $allFields = [
            $governor,
            $alliance,
            $kingdom,
            $power,
            $highestPower,
            $vip,
            $deads,
            $kills,
            $t1kills,
            $t2kills,
            $t3kills,
            $t4kills,
            $t5kills,
            $rank,
            $contribution,
            $rssGathered,
            $rssAssistance,
            $helps
        ];

        if ($this->isGranted(Role::ROLE_SUPERADMIN)) {
            $allFields[] = $snapshot;
        }

        return $allFields;
    }

    public function createEntity(string $entityFqcn)
    {
        $snapshot = new GovernorSnapshot();
        $snapshot->setCreated(new \DateTime);

        return $snapshot;
    }
}
