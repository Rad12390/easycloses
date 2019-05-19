<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street', Type\TextType::class, [
            'label' => 'Street Address Name',
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Address',
                'autofocus' => '',
                'autocomplete' => 'off'
            )
        ]);
        $builder->add('city', Type\TextType::class, [
            'label' => 'City',
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'City',
                'autofocus' => '',
                'autocomplete' => 'off'
            )
        ]);
        $builder->add('state', Type\TextType::class, [
            'label' => 'State',
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'State',
                'maxlength' => 2,
                'autofocus' => '',
                'autocomplete' => 'off'
            )
        ]);
        $builder->add('zip', Type\TextType::class, [
            'label' => 'Zip',
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Zip Code',
                'maxlength' => 5,
                'autofocus' => '',
                'autocomplete' => 'off'
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'user';
    }
}
