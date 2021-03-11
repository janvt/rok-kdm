<?php

namespace App\Form\Governor;

use App\Entity\Alliance;
use App\Entity\Governor;
use App\Entity\GovernorStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditGovernorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true
                ]
            )
            ->add(
                'altNames',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Alt Names'
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => GovernorStatus::getFormChoices(),
                    'required' => true
                ]
            )
            ->add(
                'alliance',
                EntityType::class,
                [
                    'class' => Alliance::class,
                    'placeholder' => 'None',
                    'choice_label' => function (?Alliance $alliance) {
                        return $alliance ? $alliance->getDisplayName() : '';
                    },
                    'required' => false
                ]
            )
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Governor::class,
        ]);
    }
}