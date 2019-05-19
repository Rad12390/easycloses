<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Commission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', Type\ChoiceType::class, [
            'required' => false,
            'choices' => array(
                '%' => '%', 
                '$' => '$',
                ),
            'attr' => array(
                'class' => 'form-control placeholder-no-fix custom_referral_type',
            )
        ]);
        $builder->add('value', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix custom_referral_value',
                'placeholder' => 'Amount',
                'autofocus' => ''
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commission::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'commission';
    }
}
