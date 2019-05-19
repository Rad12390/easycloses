<?php

namespace LocalsBest\MedAppUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class ForgotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', Type\TextType::class, [
            'attr' => [
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Username',
                'autofocus' => ''
            ]
        ]);
        
    }

    public function getBlockPrefix()
    {
        return 'forgot';
    }
}
