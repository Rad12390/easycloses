<?php

namespace LocalsBest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class NewFieldsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('stateLicenseId', Type\TextType::class, [
            'attr' => array(
                'placeholder' => 'State License ID',
            ),
            'required' => false,
        ]);

        $builder->add('licenseExpirationDate', Type\DateTimeType::class, [
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'License Expiration Date',
            ),
            'label' => 'License Expiration Date',
            'widget' => "single_text",
            'format' => 'MM/dd/yyyy',
            'required' => false,
        ]);

        $builder->add('joinedCompanyDate', Type\DateTimeType::class, [
            'attr' => array(
                'placeholder' => 'Joined Company Date',
            ),
            'label' => 'Joined Company Date',
            'widget' => "single_text",
            'format' => 'MM/dd/yyyy',
            'required' => false,
        ]);

        $builder->add('website', Type\TextType::class, [
            'attr' => array(
                'placeholder' => 'Website',
            ),
            'required' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'newFields';
    }
}
