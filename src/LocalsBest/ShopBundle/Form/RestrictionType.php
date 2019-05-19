<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\Restriction;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\BusinessRepository;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\IndustryTypeRepository;
use LocalsBest\UserBundle\Entity\Role;
use LocalsBest\UserBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestrictionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $business = $options['business'];
        $states = $options['states'];
        $builder
            ->add('type', Type\ChoiceType::class, [
                'choices' => [
                   // 'view' => 'View',
                 //   'purchase' => 'Purchase',
                    'Use' => 'use'
                ],
                'required' => false,
            ])
            ->add('buyerType', Type\ChoiceType::class, [
                'multiple' => false,
                'label' => 'Buyer Type',
                'expanded' => true,
                'choices' => [
                    'Owner/Manager' => "type1",
                    'Business People' => "type2",
                    'Home Services' => "type3"
                ],
                "label_attr" => array(
                    "class" => "buyer_type"
                )
            ])
            ->add('roles', EntityType::class, [
                'required' => false,
                'class' => Role::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'select2',
                ],
                'multiple' => true,
            ])
            ->add('industriesSwitch', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'label' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'icheck-inline',
                ],
                'choices' => [
                    'All' => '',
                    'Include' => 'enable',
                    'Exclude' => 'disable',
                ]
            ])
            ->add('industries', EntityType::class, [
                'required' => false,
                'class' => IndustryType::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'select2',
                ],
                'multiple' => true,
                'query_builder'=>function(IndustryTypeRepository $er){
                    return $er ->createQueryBuilder('u')->orderBy('u.name','ASC');
                }
            ])
            ->add('businessesSwitch', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'label' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'icheck-inline',
                ],
                'choices' => [
                    'All' => '',
                    'Include' => 'enable',
                    'Exclude' => 'disable',
                ]
            ])
            ->add('businesses', EntityType::class, [
                'required' => false,
                'class' => Business::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'select2',
                ],
                'multiple' => true,
                'query_builder'=>function(BusinessRepository $er) use ($business){
                    return $er ->createQueryBuilder('b')
                             ->join('b.address', 'a')
                             ->where('a.state in (:states) AND a.city in (:cities)')
                             ->setParameter(':states', $business->getAddress()->getState())
                             ->setParameter(':cities', $business->getAddress()->getCity())
                             ->orderBy('b.name','ASC');
                }
            ])
            ->add('states', EntityType::class, [
                'required' => false,
                'class' => State::class,
                'choice_label' => 'shortName',
                'attr' => [
                    'class' => 'select2',
                ],
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) use ($states) {
                    $qb = $er->createQueryBuilder('s');

                    if ($states) {
                        $qb
                        ->where('s.id IN (:state)')
                        ->setParameter('state', $states, Connection::PARAM_INT_ARRAY);
                    }

                    return $qb;
                },
            ])
            ->add('citiesSwitch', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'label' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'icheck-inline',
                ],
                'choices' => [
                    'All' => '',
                    'Include' => 'enable',
                    'Exclude' => 'disable',
                ]
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Restriction::class,
            'business' => null,
            'states'=> null,
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_restriction';
    }
}
