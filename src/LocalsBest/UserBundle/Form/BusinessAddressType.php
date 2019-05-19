<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessAddressType extends AbstractType
{
    private function getChoices()
    {
        return [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AS' => 'American Samoa',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'GU' => 'Guam',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MH' => 'Marshall Islands',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'MP' => 'Northern Mariana Islands',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PW' => 'Palau',
            'PA' => 'Pennsylvania',
            'PR' => 'Puerto Rico',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VI' => 'Virgin Islands',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        ];
    }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('street', Type\TextType::class, [
        'label' => 'Street Address Name',
        'required' => false,
        'attr' => array(
            'class' => 'form-control placeholder-no-fix',
            'placeholder' => ' Address',
            'autofocus' => '',
            'autocomplete' => 'off'
        )
    ]);
    $builder->add('city', Type\TextType::class, [
        'label' => 'City',
        'required' => false,
        'attr' => array(
            'class' => 'form-control placeholder-no-fix',
            'placeholder' => ' City',
            'autofocus' => '',
            'autocomplete' => 'off'
        )
    ]);
    $builder->add('state', Type\ChoiceType::class, [
        'choices' => array_flip($this->getChoices()),
        'label' => 'State',
        'placeholder' => false,
        'required' => false,
        'attr' => [
            'class' => 'form-control placeholder-no-fix',
        ]
    ]);
    $builder->add('zip', Type\TextType::class, [
        'label' => 'Zip',
        'required' => false,
        'empty_data' => false,
        'attr' => array(
            'class' => 'form-control placeholder-no-fix',
            'placeholder' => 'Zip Code',
            'maxlength' => 5,
            'autofocus' => '',
            'autocomplete' => 'off'
        )
    ]);
  }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'user';
    }
}
