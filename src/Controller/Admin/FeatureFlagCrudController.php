<?php

namespace App\Controller\Admin;

use App\Entity\FeatureFlag;
use App\Entity\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FeatureFlagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FeatureFlag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Feature Flag')
            ->setEntityLabelInPlural('Feature Flags')
            ->setEntityPermission(Role::ROLE_SUPERADMIN)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $uid = TextField::new('uid');
        $active = BooleanField::new('active');
        $roles = ChoiceField::new('roles')
            ->setChoices($this->getRoleChoices())
            ->allowMultipleChoices()
            ->setPermission(Role::ROLE_EDIT_ROLES);

        yield $uid;
        yield $active;
        yield $roles;
    }

    private function getRoleChoices(): array {
        $roleChoices = [];
        foreach (Role::ALL as $role) {
            $roleChoices[$role] = $role;
        }

        return $roleChoices;
    }
}
