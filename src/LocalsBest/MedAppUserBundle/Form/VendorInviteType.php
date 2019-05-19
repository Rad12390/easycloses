<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class VendorInviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('emails', Type\TextType::class, [
            'attr' => array(
                'class' => 'form-control placeholder-no-fix add_email',
                'placeholder' => 'Email',
                'autofocus' => ''
            )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'vendor_invite';
    }
}
