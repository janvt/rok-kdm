<?php

namespace App\Form\Governor;

use App\Entity\EquipmentInventory;
use App\Entity\EquipmentLoadout;
use App\Repository\EquipmentInventoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipmentLoadoutType extends AbstractType
{
    private $equipmentInventoryRepo;

    public function __construct(EquipmentInventoryRepository $equipmentInventoryRepo)
    {
        $this->equipmentInventoryRepo = $equipmentInventoryRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add('save', SubmitType::class)
            ->add('saveAndReturn', SubmitType::class)
        ;

        foreach(EquipmentInventory::SLOTS as $slot) {
            $isAccessorySlot = \strpos($slot, 'Acc') !== false;
            $dbKey = $isAccessorySlot ? 'accessory' : \strtolower($slot);

            $builder
                ->add(
                    'slot' . $slot,
                    ChoiceType::class,
                    [
                        'required' => false,
                        'label' => $isAccessorySlot ? 'Accessory' : $slot,
                        'choices' => $this->getSlotChoices($dbKey)
                    ]
                )
                ->add(
                    'slot' . $slot . 'Special',
                    ChoiceType::class,
                    [
                        'required' => false,
                        'label' => 'Special Talent',
                        'choices' => EquipmentInventory::SPECIAL_TALENT_CHOICES
                    ]
                );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquipmentLoadout::class,
        ]);
    }

    private function getSlotChoices(string $slot): array
    {
        $choices = [];

        foreach($this->equipmentInventoryRepo->findBy(['slot' => $slot]) as $item) {
            $choices[$item->getName()] = $item;
        }

        return $choices;
    }
}