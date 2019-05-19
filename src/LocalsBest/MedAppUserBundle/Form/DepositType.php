<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Deposit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix deposit_box currency',
                'placeholder' => '$ Amount',
                'autofocus' => ''
            )
        ]);
        $builder->add('date', Type\DateTimeType::class, [
            'required' => false,
            'widget' => "single_text",
            'format' => 'MM/dd/yyyy',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'MM/DD/YYYY',
                'autofocus' => ''
            )
        ]);
        $builder->add('depositAlert', Type\CheckboxType::class, [
            'attr' => array('class'     => 'show-deposit-alert'),
            'required'  => false,
        ]);
        $builder->add('alerts', Type\CollectionType::class, [
            'entry_type' => DepositAlertType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deposit::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'deposit';
    }
}
