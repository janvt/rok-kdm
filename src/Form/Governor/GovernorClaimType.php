<?php


namespace App\Form\Governor;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class GovernorClaimType extends AbstractType
{
    const MAX_SIZE = '10M';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'required' => false,
                    'label' => 'Please submit a governor profile screenshot with the governor id, name and alliance visible.',
                    'mapped' => false,
                    'constraints' => [
                        new Image([
                            'maxSize' => self::MAX_SIZE,
                            'mimeTypes' => [
                                'image/*',
                            ]
                        ])
                    ],
                ]
            )
            ->add('submit', SubmitType::class)
        ;
    }
}