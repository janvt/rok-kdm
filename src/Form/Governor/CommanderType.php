<?php

namespace App\Form\Governor;

use App\Entity\Commander;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CommanderType extends AbstractType
{
    const SKILL_ERROR = '4x [0-5]';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'level',
                IntegerType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Range([
                            'min' => 1,
                            'max' => 60
                        ])
                    ],
                ]
            )
            ->add(
                'skills',
                TextType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Callback(function($skill, ExecutionContextInterface $context): bool {
                            if ($skill === null) {
                                return true;
                            }

                            if (strlen($skill) != 4) {
                                $context->addViolation(self::SKILL_ERROR);
                                return false;
                            }

                            foreach (\str_split($skill) as $char) {
                                if (!\in_array($char, ['0', '1', '2', '3', '4', '5'], true)) {
                                    $context->addViolation(self::SKILL_ERROR);
                                    return false;
                                }
                            }

                            return true;
                        })
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commander::class,
        ]);
    }
}