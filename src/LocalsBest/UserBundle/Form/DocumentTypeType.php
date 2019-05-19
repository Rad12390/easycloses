<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class, [
            'required' => true,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'autofocus' => ''
            )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'documentType';
    }
}
