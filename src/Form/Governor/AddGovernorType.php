<?php

namespace App\Form\Governor;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddGovernorType extends EditGovernorType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'governorId',
                TextType::class,
                [
                    'required' => true
                ]
            )
        ;

        parent::buildForm($builder, $options);
    }
}