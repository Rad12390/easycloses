<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Preference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('mail', Type\ChoiceType::class, [
                'choices'  => array( 
                    'Yes' => true,
                    'No' => false
            ), 
            'expanded' => true, 
            'multiple' => false, 
        ]);

        $builder
            ->get('mail')
            ->addModelTransformer(new CallbackTransformer(
                function($mypropertyAsBoolean) {
                    return $mypropertyAsBoolean ? "1" : "0";
                },
                function($mypropertyAsString) {
                    return $mypropertyAsString == 1 ? true : false;
                }
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Preference::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'preference';
    }
}
