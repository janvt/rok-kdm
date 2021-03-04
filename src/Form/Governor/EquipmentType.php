<?php

namespace App\Form\Governor;

use App\Entity\Commander;
use App\Entity\Equipment;
use App\Service\Governor\EquipmentNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'crafted',
                CheckboxType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'blueprint',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Blueprint'
                ]
            )
            ->add(
                'specialTalent',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => \array_flip(EquipmentNames::SPECIAL_TALENTS)
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}