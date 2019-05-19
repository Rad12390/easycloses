<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\City;
use LocalsBest\UserBundle\Entity\ClientBusiness;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientBusinessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Business Name',
                'required' => false,
            ])
            ->add('managerName', Type\TextType::class, [
                'required' => false,
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Business Email',
                'required' => false,
            ])
            ->add('phone', Type\TextType::class, [
                'label' => 'Business Phone',
                'required' => false,
            ])
            ->add('website', Type\TextType::class, [
                'label' => 'Business Website',
                'required' => false,
            ])
            ->add('industry_type', EntityType::class, [
                'class' => IndustryType::class,
                'choice_label' => 'name',
                'required' => false,
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add('states', EntityType::class, [
                'class' => State::class,
                'choice_label' => 'name',
                'required' => false,
                'expanded' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2',
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $clientBusiness = $event->getData();
            $form = $event->getForm();

            if ($clientBusiness->getId() === null) {
                $form
                    ->add('cities', Type\ChoiceType::class, [
                        'required' => false,
                        'mapped' => false,
                        'expanded' => false,
                        'multiple' => true,
                        'choices' => [],
                        'attr' => [
                            'class' => 'select2_remote',
                        ]
                    ])
                ;
            } else {
                $data = $clientBusiness->getData();
                $form
                    ->add('cities', Type\ChoiceType::class, [
                        'required' => false,
                        'mapped' => false,
                        'expanded' => false,
                        'multiple' => true,
                        'data' => array_keys($data),
                        'choices' => $data,
                        'attr' => [
                            'class' => 'select2_remote',
                        ]
                    ])
                ;
            }

            $form->add('submit', Type\SubmitType::class, [
                'label' => "Submit",
                'attr' => [
                    'class' => 'btn-primary'
                ],
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $clientBusiness = $event->getData();

            $form
                ->add('cities', EntityType::class, [
                    'class' => City::class,
                    'query_builder' => function (EntityRepository $er) use ($clientBusiness) {
                        if (isset($clientBusiness['cities'])) {
                            return $er->createQueryBuilder('c')
                                ->where('c.id in (:ids)')
                                ->setParameter('ids', $clientBusiness['cities'])
                                ->orderBy('c.name', 'ASC');
                        } else {
                            return $er->createQueryBuilder('c')
                                ->where('c.id < 0')
                                ->orderBy('c.name', 'ASC');
                        }
                    },
                    'choice_label' => 'name',
                    'required' => false,
                    'expanded' => false,
                    'multiple' => true,
                    'attr' => [
                        'class' => 'select2_remote',
                    ]
                ])
            ;
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClientBusiness::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_clientbusiness';
    }
}
