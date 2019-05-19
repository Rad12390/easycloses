<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewRegisterUser1Type extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userType', Type\ChoiceType::class, [
                'mapped' => 'false',
                'required' => false,
                'choices' => [
                    'Manager' => '1',
                    'Worker' => '2'
                ],
                'placeholder' => false,
            ])

            ->add('firstName', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'First Name',
                )
            ])

            ->add('lastName', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Last Name',
                )
            ])

            ->add('primaryEmail', EmailType::class, [
                'label' => false,
            ])

            ->add('primaryPhone', PhoneType::class, [
                'label' => false,
            ])

            ->add('username', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'User Name',
                )
            ])

            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'required' => false,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'first_options' => array('label' => 'Password','attr' => array('placeholder' => 'Password')),
                'second_options' => array('label' => 'Repeat Password','attr' => array('placeholder' => 'Repeat Password')),
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'register';
    }
}
