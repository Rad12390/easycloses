<?php

namespace LocalsBest\AdminBundle\Form;

use LocalsBest\UserBundle\Entity\Advertisement;
use LocalsBest\UserBundle\Entity\Business;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertisementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url')
            ->add('title')
            ->add('isActive', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('isNewTab', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('forBusiness', EntityType::class, [
                'required' => false,
                'class' => Business::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'select2_businesses'
                ],
            ])
            ->add('forPage', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'Home' => 'home',
                    'Transaction' => 'transaction',
                ],
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forStatus', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'Job' => [
                        'New' => 'New',
                        'Open' => 'Open',
                        'Closed' => 'Closed',
                        'Updated' => 'Updated',
                    ],
                    'Transaction' => [
                        'Pending' => 'Pending',
                        'Active' => 'Active',
                        'Cancelled' => 'Cancelled',
                        'Expired' => 'Expired',
                        'Leased' => 'Leased',
                        'Withdrawn' => 'Withdrawn',
                        'Contract fell thru' => 'Contract_fell_thru',
                        'Leased not Paid' => 'Leased_not_Paid',
                        'Leased Paid' => 'Leased_Paid',
                        'Sold not Paid' => 'Sold_not_Paid',
                        'Sold Paid' => 'Sold_Paid',
                        'Rejected Offer' => 'Rejected_Offer',
                    ],
                ],
                'choices_as_values' => true,
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forRepresent', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'Buyer' => 'Buyer',
                    'Seller' => 'Seller',
                    'Buyer&Seller' => 'Buyer&Seller',
                    'Landlord' => 'Landlord',
                    'Tenant' => 'Tenant',
                    'Landlord&Tenant' => 'Landlord&Tenant',
                ],
                'attr' => [
                    'class' => 'select2'
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $advertisement = $event->getData();
            $form = $event->getForm();

            if (!$advertisement || null === $advertisement->getId()) {
                $form->add('image', Type\FileType::class, [
                    'attr' => [
                        'accept' => 'image/jpeg, image/gif, image/png'
                    ],
                    'required' => true
                ]);
            } else {
                $form->add('image', Type\FileType::class, [
                    'attr' => [
                        'accept' => 'image/jpeg, image/gif, image/png'
                    ],
                    'required' => false
                ]);
            }
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Advertisement::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_advertisement';
    }
}
