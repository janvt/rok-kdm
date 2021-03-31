<?php

namespace App\Form\Export;

use App\Entity\Alliance;
use App\Entity\GovernorStatus;
use App\Entity\Snapshot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportGovDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'alliance',
                EntityType::class,
                [
                    'class' => Alliance::class,
                    'placeholder' => 'All',
                    'choice_label' => function (?Alliance $alliance) {
                        return $alliance ? $alliance->getDisplayName() : '';
                    },
                    'required' => false
                ]
            )
            ->add(
                'snapshot',
                EntityType::class,
                [
                    'class' => Snapshot::class,
                    'placeholder' => 'All',
                    'choice_label' => function (?Snapshot $snapshot) {
                        return $snapshot ? $snapshot->getName() . '(' . $snapshot->getUid() . ')' : '';
                    },
                    'required' => false
                ]
            )
            ->add(
                'govStatus',
                ChoiceType::class,
                [
                    'choices' => GovernorStatus::getFormChoices(),
                    'placeholder' => 'All',
                    'label' => 'Governor Status',
                    'required' => false
                ]
            )
            ->add(
                'export',
                SubmitType::class,
                [
                    'label' => 'Download CSV'
                ]
            )
        ;
    }
}