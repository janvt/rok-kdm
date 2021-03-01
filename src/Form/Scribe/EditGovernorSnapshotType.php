<?php

namespace App\Form\Scribe;

use App\Entity\GovernorSnapshot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditGovernorSnapshotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('power')
            ->add('highestPower')
            ->add('deads')
            ->add('kills')
            ->add('t1Kills')
            ->add('t2Kills')
            ->add('t3Kills')
            ->add('t4Kills')
            ->add('t5Kills')
            ->add('rssGathered')
            ->add('rssAssistance')
            ->add('helps')
            ->add('save', SubmitType::class)
            ->add('saveAndReturn', SubmitType::class)
            ->add('saveAndMarkCompleted', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GovernorSnapshot::class,
        ]);
    }
}