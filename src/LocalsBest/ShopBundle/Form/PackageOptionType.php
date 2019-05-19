<?php

namespace LocalsBest\ShopBundle\Form;

use LocalsBest\ShopBundle\Entity\PackageOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackageOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('optionsValues')
        ;
        $builder->get('optionsValues')
            ->addModelTransformer(new CallbackTransformer(
                function ($valuesAsArray) {
                    // transform the array to a string
                    return !is_null($valuesAsArray) ? implode(',', $valuesAsArray): [];
                },
                function ($valuesAsString) {
                    // transform the string back to an array
                    return explode(',', $valuesAsString);
                }
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PackageOption::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_packageoption';
    }
}
