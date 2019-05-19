<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Dbal\Types\StatusType;
use LocalsBest\UserBundle\Entity\Role;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', Type\TextType::class, [
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'first Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('lastName', Type\TextType::class, [
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'last Name',
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
            'required'=> false,
            'invalid_message' => 'The password fields must match.',
            'options' => array('attr' => array('class' => 'password-field')),
            'first_options' => array('label' => 'Password','attr' => array('placeholder' => 'password','class' => 'form-control placeholder-no-fix')),
            'second_options' => array('label' => 'Repeat Password','attr' => array('placeholder' => 'Repeat password','class' => 'form-control placeholder-no-fix')),
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
        
        $builder->add('status', Type\ChoiceType::class, [
            'label' => 'Type :',
            'choices'   => array_flip(StatusType::getChoices()),
            'required'  => true,
            'placeholder' => 'Choose an option',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'autofocus' => ''
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'user';
    }
}
