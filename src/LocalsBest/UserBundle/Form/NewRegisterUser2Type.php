<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewRegisterUser2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'First Name',
                ),
                'constraints' => array(
                    new NotBlank(),
                ),
            ])

            ->add('lastName', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Last Name',
                ),
                'constraints' => array(
                    new NotBlank(),
                ),
            ])

            ->add('primaryEmail', EmailType::class, [
                'label' => false,
                'constraints' => array(
                    new NotBlank(),
                    new Email(),
                ),
            ])

            ->add('primaryPhone', PhoneType::class, [
                'label' => false,
                'constraints' => array(
                    new NotBlank(),
                ),
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    public function getBlockPrefix()
    {
        return 'register';
    }
}
