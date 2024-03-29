<?php

namespace App\Form\Scribe;

use App\Entity\Role;
use App\Entity\Snapshot;
use App\Service\Import\ImportPreview;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigureImportType extends AbstractType
{
    private $authChecker;

    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isScribeAdmin = $this->authChecker->isGranted(Role::ROLE_SCRIBE_ADMIN);

        $allowedStatus = [Snapshot::STATUS_ACTIVE];
        if ($isScribeAdmin) {
            $allowedStatus[] = Snapshot::STATUS_COMPLETED;
        }

        $builder
            ->add('idMapping', TextType::class, ['label' => 'id', 'required' => true])
            ->add('nameMapping', TextType::class, ['label' => 'name', 'required' => false])
            ->add('statusMapping', TextType::class, ['label' => 'status', 'required' => false])
            ->add('allianceMapping', TextType::class, ['label' => 'alliance', 'required' => false])
            ->add('powerMapping', TextType::class, ['label' => 'power', 'required' => false])
            ->add('highest_powerMapping', TextType::class, ['label' => 'highest', 'required' => false])
            ->add('vipMapping', TextType::class, ['label' => 'vip', 'required' => false])
            ->add('killsMapping', TextType::class, ['label' => 'kills', 'required' => false])
            ->add('t1killsMapping', TextType::class, ['label' => 't1', 'required' => false])
            ->add('t2killsMapping', TextType::class, ['label' => 't2', 'required' => false])
            ->add('t3killsMapping', TextType::class, ['label' => 't3', 'required' => false])
            ->add('t4killsMapping', TextType::class, ['label' => 't4', 'required' => false])
            ->add('t5killsMapping', TextType::class, ['label' => 't5', 'required' => false])
            ->add('deadsMapping', TextType::class, ['label' => 'deads', 'required' => false])
            ->add('rss_gatheredMapping', TextType::class, ['label' => 'rss gathered', 'required' => false])
            ->add('rss_assistanceMapping', TextType::class, ['label' => 'rss assistance', 'required' => false])
            ->add('helpsMapping', TextType::class, ['label' => 'helps', 'required' => false])
            ->add('rankMapping', TextType::class, ['label' => 'rank', 'required' => false])
            ->add('contributionMapping', TextType::class, ['label' => 'contribution', 'required' => false])
            ->add('addNewGovernors', CheckboxType::class, ['required' => false ])
            ->add(
                'snapshot',
                EntityType::class,
                [
                    'class' => Snapshot::class,
                    'query_builder' => function (EntityRepository $er) use ($allowedStatus) {
                        return $er->createQueryBuilder('s')
                            ->where('s.status IN(:status)')
                            ->setParameter('status', $allowedStatus);
                    },
                    'placeholder' => $isScribeAdmin ? 'DANGER: This import is not attached to any snapshot.' : false,
                    'choice_label' => function (?Snapshot $snapshot) {
                        return $snapshot ? $snapshot->getName() . '(' . $snapshot->getUid() . ')' : '';
                    },
                    'required' => false
                ]
            )
            ->add('update', SubmitType::class, ['label' => 'Update Preview'])
            ->add('complete', SubmitType::class, ['label' => 'Import Data'])
            ->add('cancel', SubmitType::class, ['attr' => ['title' => 'Start Over']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ImportPreview::class
        ]);
    }
}