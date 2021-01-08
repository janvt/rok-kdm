<?php

namespace App\Controller\Admin;

use App\Entity\OfficerNote;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OfficerNoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OfficerNote::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Officer Note')
            ->setEntityLabelInPlural('Officer Notes')
            ->setSearchFields(['id', 'governor_id', 'name', 'status', 'alliance', 'user.email', 'user.discordUsername']);
    }

    public function configureFields(string $pageName): iterable
    {
        $governor = AssociationField::new('governor');
        $tldr = TextField::new('tldr');
        $note = TextField::new('note');
        $created = DateTimeField::new('created');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$governor, $created, $tldr];
        }

        return [$governor, $tldr, $note];
    }

    public function createEntity(string $entityFqcn)
    {
        $note = new OfficerNote();
        $note->setUser($this->getUser());
        $note->setCreated(new \DateTime);

        return $note;
    }
}
