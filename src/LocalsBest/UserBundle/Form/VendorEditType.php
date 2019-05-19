<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VendorEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vendorCategory', Type\ChoiceType::class, [
            'required' => true,
            'choices' => array(
                'Gold' => 3,
                'Silver' => 2,
                'bronze' => 1,
                'free' => 0,
                ),
            'placeholder' => 'Choose One',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix required_field required',
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'vendor';
    }
}
