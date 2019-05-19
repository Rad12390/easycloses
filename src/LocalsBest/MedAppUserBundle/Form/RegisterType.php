<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Role;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', Type\TextType::class, [
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'First Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('lastName', Type\TextType::class, [
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Last Name',
                'autofocus' => ''
            )
        ]);
        
        $builder->add('contact', ContactType::class);
        
        $builder->add('username', Type\TextType::class, [
            'attr' => array(
                'class' => 'form-control placeholder-no-fix add',
                'placeholder' => 'User Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('password', Type\RepeatedType::class, [
            'type' => Type\PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => array('attr' => array('class' => 'password-field')),
            'first_options' => array('label' => 'Password','attr' => array('placeholder' => 'Password','class' => 'form-control placeholder-no-fix')),
            'second_options' => array('label' => 'Repeat Password','attr' => array('placeholder' => 'Repeat Password','class' => 'form-control placeholder-no-fix')),
        ]);

        $builder->add('role', EntityType::class, [
            'class' => Role::class,
            'required' => false,
            'choice_label' => 'name',
            'placeholder' => 'None',
            'empty_data' => null,
            'attr' => array(
                'class' => 'form-control select2_category',
            )
        ]);

        $builder->add('tnc', Type\CheckboxType::class, [
            'mapped' => false,
            'label' => false,
            'constraints' => new IsTrue(),
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => true,
        ));
    }

    public function getBlockPrefix()
    {
        return 'register';
    }
}
