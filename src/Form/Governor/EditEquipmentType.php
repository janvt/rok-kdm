<?php

namespace App\Form\Governor;

use App\Entity\Governor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'equipment',
                CollectionType::class,
                [
                    'entry_type' => EquipmentType::class,
                    'entry_options' => ['label' => false],
                    'label' => false
                ]
            )
            ->add('save', SubmitType::class)
            ->add(
                'saveAndReturn',
                SubmitType::class,
                [
                    'label' => 'Save and return to profile'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Governor::class,
        ]);
    }
}