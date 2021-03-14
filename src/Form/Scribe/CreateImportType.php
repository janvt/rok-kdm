<?php

namespace App\Form\Scribe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class CreateImportType extends AbstractType
{
    const MAX_SIZE = '10M';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'csvFile',
                FileType::class,
                [
                    'required' => false,
                    'label' => 'Select .csv file',
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => self::MAX_SIZE,
                            'mimeTypes' => [
                                'application/csv',
                                'text/csv',
                                'text/plain',
                                'text/x-comma-separated-values',
                                'text/x-csv'
                            ]
                        ])
                    ],
                ]
            )
            ->add(
                'csvInput',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'or paste .csv file contents',
                    'constraints' => [
                        new Length([
                            'max' => 65500,
                            'maxMessage' => 'Please use file upload for large imports.',
                        ])
                    ]
                ]
            )
            ->add('preview', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}