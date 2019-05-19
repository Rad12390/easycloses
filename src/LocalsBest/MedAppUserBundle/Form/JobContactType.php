<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\JobContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => '',
                'autofocus' => '',
                'readonly' => true,
            )
        ]);
        
        $builder->add('email', Type\EmailType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix ajax-email',
                'placeholder' => 'Email',
                'autofocus' => ''
            )
        ]);
        $builder->add('phone', Type\TextType::class, [
            'required' => false,
            'label' => 'Phone :',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix ajax-phone',
                'placeholder' => 'Phone',
                'autofocus' => ''
            )
        ]);
        $builder->add('contactName', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix typeahead',
                'placeholder' => 'Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('company', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Company',
                'autofocus' => ''
            )
        ]);
        $builder->add('contactBy', Type\ChoiceType::class, [
            'choices' => array(
                'Phone' =>  'phone',
                'Email' =>  'email',
                'Text'  =>   'text',
                'Do not contact'    => 'do_not_contact',
            ),
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => JobContact::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'job_contact';
    }
}
