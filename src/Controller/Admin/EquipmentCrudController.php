<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Service\Governor\EquipmentNames;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Equipment')
            ->setEntityLabelInPlural('Equipment')
            ->setSearchFields(['uid', 'governor.governor_id', 'governor.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $governor = AssociationField::new('governor');
        $uid = ChoiceField::new('uid')
            ->setChoices(\array_flip(EquipmentNames::ALL));
        $crafted = BooleanField::new('crafted');
        $blueprint = BooleanField::new('blueprint');
        $specialTalent = ChoiceField::new('specialTalent')
            ->setChoices(\array_flip(EquipmentNames::SPECIAL_TALENTS));

        return [$uid, $governor, $crafted, $blueprint, $specialTalent];
    }
}
