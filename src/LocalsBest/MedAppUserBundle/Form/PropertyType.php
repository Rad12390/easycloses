<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Dbal\Types\PropertyTypeType;
use LocalsBest\UserBundle\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', Type\TextType::class, [
            'label' => 'Property Title',
            'required'  => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);

        $builder->add('address', AddressType::class);
        
        $builder->add('type', Type\ChoiceType::class, [
            'label' => 'Property Type',
            'choices'   => PropertyTypeType::getChoices(),
            'required'  => false,
            'placeholder' => 'Property Type',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'autofocus' => ''
            )
        ]);

        $builder->add('isPublic', Type\CheckboxType::class, [
            'label' => 'Show to public',
            'required'  => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);

        $builder->add('format', Type\ChoiceType::class, [
            'label' => 'Property Format',
            'required'  => false,
            'choices'   => [
                'None' => "",
                'Business' => "business",
                'Private' => "private",
            ],
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'property';
    }
}
