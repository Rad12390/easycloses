<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\EventAlert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventAlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', Type\DateTimeType::class, [
            'required' => false,
            'widget' => "single_text",
            'format' => 'MM/dd/yyyy hh:mm a',
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Alert time',
                'autofocus' => ''
            ]
        ]);
        
        $builder->add('email', Type\CheckboxType::class, [
            'required'  => false,
        ]);
        $builder->add('phone', Type\CheckboxType::class, [
            'required'  => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventAlert::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'event_alert';
    }
}
