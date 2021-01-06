<?php

namespace App\Controller\Admin;

use App\Entity\GovernorSnapshot;
use App\Entity\GovernorStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
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
            ->setSearchFields(['id', 'governor_id']);
    }

    public function configureFields(string $pageName): iterable
    {
        $governor = TextField::new('governor_id');
        $created = DateTimeField::new('created');
        $alliance = TextField::new('alliance');
        $kingdom = TextField::new('kingdom');
        $power = IntegerField::new('power');
        $highestPower = IntegerField::new('highest_power');
        $t1kills = IntegerField::new('t1_kills');
        $t2kills = IntegerField::new('t2_kills');
        $t3kills = IntegerField::new('t3_kills');
        $t4kills = IntegerField::new('t4_kills');
        $t5kills = IntegerField::new('t5_kills');
        $deads = IntegerField::new('deads');
        $helps = IntegerField::new('helps');
        $rssGathered = IntegerField::new('rss_gathered');
        $rssAssistance = IntegerField::new('rss_assistance');

        $allFields = [
            $governor,
            $alliance,
            $kingdom,
            $power,
            $highestPower,
            $t1kills,
            $t2kills,
            $t3kills,
            $t4kills,
            $t5kills,
            $deads,
            $rssGathered,
            $rssAssistance,
            $helps
        ];

        if (Crud::PAGE_INDEX === $pageName) {
            return [$governor, $alliance, $created, $power, $t4kills, $t5kills, $deads];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return $allFields;
        } elseif (Crud::PAGE_NEW === $pageName) {
            return $allFields;
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return $allFields;
        }
    }
}
