<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Dbal\Types\EventTypeType;
use LocalsBest\UserBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', Type\TextType::class, [
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Event Name',
                'autofocus' => ''
            ]
        ]);
        $builder->add('time', Type\DateTimeType::class, [
            'widget' => "single_text", 
            'format' => 'MM/dd/yyyy hh:mm a',
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Start Time',
                'autofocus' => ''
            ]
        ]);
        $builder->add('endTime', Type\DateTimeType::class, [
            'widget' => "single_text",
            'format' => 'MM/dd/yyyy hh:mm a',
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'End Time',
                'autofocus' => ''
            ]
        ]);
        $builder->add('type', Type\ChoiceType::class, [
            'choices'   => array_flip(EventTypeType::getChoices()),
            'required'  => false,
            'placeholder' => 'Event type',
            'attr' => [
                'class' => 'form-control placeholder-no-fix type-val',
                'autofocus' => ''
            ]
        ]);
        $builder->add('custom', Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Event Type',
                'autofocus' => ''
            ]
        ]);
        $builder->add('description', Type\TextareaType::class, [
            'required' => false,
            'attr' => [
                'class'=> 'form-control',
                'rows' => '3',
                'placeholder' => 'Description'
            ]
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
        return 'event';
    }
}
