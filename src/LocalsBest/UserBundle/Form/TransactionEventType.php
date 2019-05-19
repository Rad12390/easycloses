<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => '',
                'autofocus' => ''
            )
        ]);
        $builder->add('time', Type\DateTimeType::class, [
            'required' => false,
            'widget' => "single_text", 
            'format' => 'MM/dd/yyyy',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'MM/DD/YYYY',
                'autofocus' => ''
            )
        ]);
        $builder->add('alert', Type\CheckboxType::class, [
            'attr' => array('class'     => 'show-event-alert'),
            'required'  => false,
        ]);
        
        $builder->add('alerts', Type\CollectionType::class, [
            'entry_type' => EventAlertType::class,
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
            'data_class' => Event::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'transaction_event';
    }
}
