<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\TransactionContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionContactFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix typeahead',
                'placeholder' => 'Role',
                'autofocus' => ''
            )
        ]);
        $builder->add('contactName', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix typeahead',
                'placeholder' => 'Contact Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('email', Type\EmailType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix ajax-email',
                'placeholder' => 'Contact Email',
                'autofocus' => ''
            )
        ]);
        $builder->add('phone', Type\TextType::class, [
            'required' => false,
            'label' => 'Phone :',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix ajax-phone phone',
                'placeholder' => 'Contact Phone',
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
        $builder->add('companyEmail', Type\EmailType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix ajax-email',
                'placeholder' => 'Company Email',
                'autofocus' => ''
            )
        ]);
        $builder->add('companyPhone', Type\TextType::class, [
            'required' => false,
            'label' => 'Phone :',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix ajax-phone phone',
                'placeholder' => 'Company Phone',
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
            'data_class' => TransactionContact::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'contact';
    }
}
