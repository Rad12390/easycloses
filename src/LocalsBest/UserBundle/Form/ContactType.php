<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    protected $name;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->name = $options['name'];

        $builder->add('emails', Type\CollectionType::class, [
            'entry_type' => EmailType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
        
        $builder->add('phones', Type\CollectionType::class, [
            'entry_type' => PhoneType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Contact::class,
            'name' => 'contact',
        ));
    }
    
    public function getBlockPrefix()
    {
        return $this->name;
    }
}
