<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentRejectedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rejectnote', Type\TextareaType::class, [
            'label' => 'Note :',
            'required' => false,
            'attr' => array(
                'class'=> 'form-control',
                'rows' => '3',
                'placeholder' => 'Reject Note'
            )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'documentRejectedType';
    }
}
