<?php


namespace App\Form\Search;


use App\Service\Governor\CommanderNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommanderSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commanderChoices = \array_flip(CommanderNames::ALL);

        $builder
            ->setMethod('GET')
            ->add(
                'commander1',
                ChoiceType::class,
                [
                    'choices' => $commanderChoices
                ]
            )
            ->add(
                'commander2',
                ChoiceType::class,
                [
                    'choices' => $commanderChoices,
                    'placeholder' => 'None',
                    'required' => false
                ]
            )
            ->add('search', SubmitType::class);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}