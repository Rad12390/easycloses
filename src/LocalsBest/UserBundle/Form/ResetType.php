<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class ResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', Type\RepeatedType::class, [
            'type' => Type\PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options' => array('label' => 'Password','attr' => array('placeholder' => 'New Password','class' => 'form-control placeholder-no-fix')),
            'second_options' => array('label' => 'Repeat Password','attr' => array('placeholder' => 'Repeat Password','class' => 'form-control placeholder-no-fix')),
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'reset';
    }
}
