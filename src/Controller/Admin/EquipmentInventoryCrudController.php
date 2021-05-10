<?php

namespace App\Controller\Admin;

use App\Entity\EquipmentInventory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EquipmentInventoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EquipmentInventory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Equipment Inventory')
            ->setEntityLabelInPlural('Equipment')
            ->setSearchFields(['uid', 'name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $uid = TextField::new('uid');
        $name = TextField::new('name');
        $set = TextField::new('set');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$uid, $name, $set];
        }

        $fields = [];
        foreach (['cavalry', 'infantry', 'archer'] as $troopType) {
            foreach (['attack', 'defense', 'health'] as $statType) {
                $fields[] = NumberField::new($troopType . '_' . $statType);
            }
        }

        return array_merge([
            $uid,
            $name,
            $set
        ], $fields);
    }
}
