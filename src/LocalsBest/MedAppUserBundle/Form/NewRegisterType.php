<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewRegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user1', NewRegisterUser1Type::class)
            ->add('user2', NewRegisterUser2Type::class)
            ->add('business', NewRegisterBusinessType::class)
            ->add('submit', Type\SubmitType::class, [
                'label' => 'Register',
                'attr' => [
                    'class' => 'green button-submit pull-right',
                ]
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
        return 'new_register';
    }

}
