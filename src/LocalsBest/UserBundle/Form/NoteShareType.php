<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class NoteShareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('checkName', Type\CheckboxType::class, [
            'required'  => false,
        ]);
        
        $builder->add('contactName', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix typeahead',
            )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'add_note_share';
    }
}
