<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\DepositAlert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositAlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', Type\DateTimeType::class, [
            'required' => false,
            'widget' => "single_text",
            'format' => 'MM/DD/YYYY h:m a',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Alert Time',
                'autofocus' => ''
            )
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
            'data_class' => DepositAlert::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'deposit_alert';
    }
}
