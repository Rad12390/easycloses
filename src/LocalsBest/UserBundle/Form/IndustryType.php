<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class IndustryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class, [
            'label' => 'Industry Type Name',
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'required' => false,
                'placeholder' => 'Industry Type Name',
                'autofocus' => ''
            ]
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'industryType';
    }
}