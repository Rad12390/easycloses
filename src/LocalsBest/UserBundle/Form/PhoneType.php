<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Dbal\Types\PhoneTypeType;
use LocalsBest\UserBundle\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number', Type\TextType::class, [
            'label' => 'Phone:',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix phone',
                'placeholder' => 'Phone',
                'autofocus' => ''
            )
        ]);
        
        $builder->add('type', Type\ChoiceType::class, [
            'label' => 'Type:',
            'choices'   => PhoneTypeType::getChoices(),
            'placeholder' => 'Choose an option',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'autofocus' => ''
            )
        ]);
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Phone::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'phone';
    }
}
