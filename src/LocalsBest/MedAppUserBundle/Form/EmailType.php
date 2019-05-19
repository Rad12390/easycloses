<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', Type\TextType::class, [
            'label' => 'Email:',
            'attr' => [
                'class' => 'form-control placeholder-no-fix add_email',
                'placeholder' => 'Email',
                'autofocus' => '',
                'pattern'=> '[a-z0-9._%+-]+@[a-z0-9._%+-]+\.[a-z]{2,}$',
                'oninvalid'=>"setCustomValidity('You must use this format: abc@example.com')",
                'oninput'=> "setCustomValidity('')"
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Email::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'useremail';
    }
}
