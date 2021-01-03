<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
            ->setSearchFields(['id', 'email', 'username', 'slug']);
    }

    public function configureFields(string $pageName): iterable
    {
        $email = TextField::new('email');
        $username = TextField::new('username');
        $slug = TextField::new('slug');
        $roles = ArrayField::new('roles');
        $governors = AssociationField::new('governors');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $email, $username, $roles, $governors];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $email, $username, $slug, $roles, $governors];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$email, $username, $slug, $roles, $governors];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$email, $username, $slug, $roles, $governors];
        }
    }
}
