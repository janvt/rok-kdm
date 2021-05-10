<?php

namespace App\Controller\Admin;

use App\Entity\EquipmentInventory;
use App\Entity\EquipmentLoadout;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EquipmentLoadoutCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EquipmentLoadout::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Equipment Loadouts')
            ->setEntityLabelInPlural('Equipment Loadout')
            ->setSearchFields(['name', 'governor.id', 'governor.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id');
        $name = TextField::new('name');
        $governor = AssociationField::new('governor');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $governor];
        }

        $slotFields = [];
        foreach (EquipmentInventory::SLOTS_DB as $slot) {
            $slotFields[] = AssociationField::new('slot_' . $slot);
            $slotFields[] = ChoiceField::new('slot_' . $slot . '_special')
                ->setChoices(EquipmentInventory::SPECIAL_TALENT_CHOICES);
        }

        return array_merge([$name, $governor], $slotFields);
    }
}
