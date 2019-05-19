<?php

namespace LocalsBest\ShopBundle\Form;

use LocalsBest\ShopBundle\Entity\SkuContactUs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkuContactUsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serviceName', Type\TextType::class, [
                'required' => false,
                'label' => 'Service/Product',
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('userName', Type\TextType::class, [
                'label' => 'Full Name',
                'label_attr' => [
                    'class' => 'color-change'
                ]
            ])
            ->add('email', null, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'color-change'
                ]
            ])
            ->add('phone', null, [
                'label' => 'Phone',
                'label_attr' => [
                    'class' => 'color-change'
                ]
            ])
            ->add('note', null, [
                'label' => 'Add a Note',
                'label_attr' => [
                    'class' => 'color-change'
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
            'data_class' => SkuContactUs::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_skucontactus';
    }
}
