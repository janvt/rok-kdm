<?php

namespace App\Controller\Admin;

use App\Entity\Commander;
use App\Service\Governor\CommanderNames;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommanderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commander::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commander')
            ->setEntityLabelInPlural('Commanders')
            ->setSearchFields(['uid', 'level', 'skills']);
    }

    public function configureFields(string $pageName): iterable
    {
        $governor = AssociationField::new('governor');
        $uid = ChoiceField::new('uid')
            ->setChoices(\array_flip(CommanderNames::ALL));
        $level = IntegerField::new('level');
        $skills = TextField::new('skills');

        return [$uid, $governor, $level, $skills];
    }
}
