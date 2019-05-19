<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class PaidPreferenceType extends PreferenceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('sms', Type\ChoiceType::class, array(
            'choices'  => array(
                'Yes' => true,
                'No' => false,
            ), 
            'expanded' => true, 
            'multiple' => false, 
        ));

        $builder
            ->get('sms')
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

}
