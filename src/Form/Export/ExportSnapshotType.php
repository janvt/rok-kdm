<?php

namespace App\Form\Export;

use App\Entity\Snapshot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportSnapshotType extends ExportAllType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'snapshot',
                EntityType::class,
                [
                    'class' => Snapshot::class,
                    'choice_label' => function (?Snapshot $snapshot) {
                        return $snapshot ? $snapshot->getName() . '(' . $snapshot->getUid() . ')' : '';
                    },
                    'required' => true
                ]
            )
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Snapshot::class,
        ]);
    }
}