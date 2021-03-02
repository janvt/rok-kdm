<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('User')
            ->setSearchFields(['id', 'email', 'discordUsername', 'governors.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $hasRoleAdmin = $this->isGranted(Role::ROLE_ADMIN);

        $id = IntegerField::new('id');
        $email = TextField::new('email');
        $discordDisplayName = TextField::new('discordDisplayName', 'Discord');
        $roles = ChoiceField::new('roles')
            ->setChoices($this->getRoleChoices())
            ->allowMultipleChoices()
            ->setPermission(Role::ROLE_EDIT_ROLES);
        $governors = AssociationField::new('governors');

        if (Crud::PAGE_INDEX === $pageName) {
            if ($hasRoleAdmin) {
                yield $id;
            }

            yield $email;
            yield $discordDisplayName;
        }

        yield $email;
        if ($hasRoleAdmin) {
            yield $governors;
        }

        if ($this->isGranted(Role::ROLE_EDIT_ROLES)) {
            yield $roles;
        }
    }

    private function getRoleChoices(): array {
        $roleChoices = [];
        foreach (Role::ALL as $role) {
            $roleChoices[$role] = $role;
        }

        return $roleChoices;
    }
}
