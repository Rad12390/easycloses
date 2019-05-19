<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('fullName', Type\TextType::class, [
                'label' => 'Name',
                'data' => $user === null ? '' : $user->getFullName(),
                'attr' => [
                    'placeholder' => 'e.g. John Doe',
                    'readonly' => true,
                ],
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'E-mail',
                'data' => $user === null ? '' : $user->getPrimaryEmail()->getEmail(),
                'attr' => [
                    'placeholder' => 'e.g. johndoe@gmail.com',
                    'readonly' => true,
                ],
            ])
            ->add('body', Type\TextareaType::class, [
                'label' => 'Review'
            ])
            ->add('rating', Type\NumberType::class, [
                'data' => 3,
                'attr' => [
                    'class' => 'kv-fa',
                    'data-size' => "sm",

                ]
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn-primary'
                ],
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Feedback::class,
            'user' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_feedback';
    }
}
