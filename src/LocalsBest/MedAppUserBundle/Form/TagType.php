<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tag', Type\TextType::class, [
            'label' => 'Tag :',
            'required' => false,
            'attr' => array(
                'class'=> 'form-control tags_1',
                'placeholder' => 'Any Tag'
            )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'add_note';
    }
}
