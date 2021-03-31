<?php

namespace App\Form\Export;

use App\Entity\Alliance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportCommandersType extends AbstractType
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
                'export',
                SubmitType::class,
                [
                    'label' => 'Download CSV'
                ]
            )
        ;
    }
}