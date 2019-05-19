<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentpassword', Type\PasswordType::class, [
                'label' => 'Current Password',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Current password',
                ),
                'mapped' => false,
                'constraints' => new UserPassword()
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array(
                    'attr' => array(
                        'class' => 'password-field'
                    )
                ),
                'required' => false,
                'first_options' => array(
                    'label' => 'Password',
                    'attr' => array(
                        'placeholder' => 'New Password',
                    )
                ),
                'second_options' => array(
                    'label' => 'Repeat Password',
                    'attr' => array(
                        'placeholder' => 'Repeat password',
                    )
                ),
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'change';
    }
}
