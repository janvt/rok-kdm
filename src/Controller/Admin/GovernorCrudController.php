<?php

namespace App\Controller\Admin;

use App\Entity\Alliance;
use App\Entity\Governor;
use App\Entity\GovernorStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GovernorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Governor::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Governor')
            ->setEntityLabelInPlural('Governor')
            ->setSearchFields([
                'id',
                'governor_id',
                'name',
                'status',
                'alliance.name',
                'alliance.tag',
                'user.email',
                'user.discordUsername'
            ]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('status')
            ->add('alliance')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $statusChoices = [];
        foreach (GovernorStatus::GOV_STATUSES as $status) {
            $statusChoices[$status] = $status;
        }

        $governorId = TextField::new('governor_id');
        $name = TextField::new('name');
        $status = ChoiceField::new('status')
            ->setChoices($statusChoices)
            ->allowMultipleChoices(false);
        $user = AssociationField::new('user');
        $snapshots = AssociationField::new('snapshots');
        $alliance = AssociationField::new('alliance')
            ->setFieldFqcn(Alliance::class);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$governorId, $name, $status, $alliance, $user];
        }

        return [$governorId, $name, $status, $alliance, $user, $snapshots];
    }

    public function createEntity(string $entityFqcn)
    {
        return new $entityFqcn();
    }
}
