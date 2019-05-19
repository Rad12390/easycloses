<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\TransactionContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'class' => 'form-control placeholder-no-fix ajax-phone phone',
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
        $builder->add('address', AddressType::class);
        $builder->add('company', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Company',
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
