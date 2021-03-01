<?php

namespace App\Controller\Admin;

use App\Entity\Alliance;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AllianceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Alliance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Alliance')
            ->setEntityLabelInPlural('Alliances')
            ->setSearchFields(['tag', 'name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $tag = TextField::new('tag');
        $name = TextField::new('name');
        $type = ChoiceField::new('type')
            ->setChoices([
                'Main Alliance' => Alliance::TYPE_MAIN,
                'Farm Alliance' => Alliance::TYPE_FARM,
                'shell' => Alliance::TYPE_SHELL
            ])
            ->allowMultipleChoices(false);

        yield $tag;
        yield $name;
        yield $type;
    }
}
