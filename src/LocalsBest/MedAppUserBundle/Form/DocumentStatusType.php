<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('response', Type\ChoiceType::class, [
                'choices'  => array( 
                        'File it'  => 1,
                        'Decline' => -1
            ),
            'expanded' => true, 
            'multiple' => false, 
        ]);
        
        $builder->add('note', Type\TextareaType::class, [
            'label' => 'Note :',
            'required' => false,
            'attr' => array(
                'class'=> 'form-control',
                'rows' => '3',
                'placeholder' => 'Any Note'
            )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'document_status_confirmation';
    }
}
