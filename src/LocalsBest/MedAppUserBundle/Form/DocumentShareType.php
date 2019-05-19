<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Document;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentShareType extends AbstractType
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shares', Type\CollectionType::class, [
            'entry_type'    => new ShareType($this->user),
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference'  => false,
            'prototype'     => true,
        ]);
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'document_share';
    }
}
