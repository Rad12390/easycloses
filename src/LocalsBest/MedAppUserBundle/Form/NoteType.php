<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('note', Type\TextareaType::class, [
            'label' => 'Note :',
            'required' => false,
            'attr' => array(
                'class'=> 'form-control',
                'rows' => '3',
                'placeholder' => 'Add Note'
            )
        ]);
        $builder->add('important', Type\CheckboxType::class, [
            'label'     => 'Mark as a Important Note',
            'required'  => false,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'add_note';
    }
}
