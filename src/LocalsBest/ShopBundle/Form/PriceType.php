<?php

namespace LocalsBest\ShopBundle\Form;

use LocalsBest\ShopBundle\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
		 ->add('type', Type\ChoiceType::class, [
                'multiple' => false,
                'expanded'=>true,
                'label' => false,
                'choices' => [
                    'One Time Payment' => "onetime",
                    'Subscription' => "subscription"
                ],
		    // 'data'=> 'onetime',
                'attr' => [
                    'class' => 'price-type',
			                  ]
                ])
		 ->add('retailPrice', Type\TextType::class, [
                'label' => 'Retail Price',
                'required'=>true,
                'attr' => [
                    'class' => 'retail',
                    'data-index' => '__name__',
                    'placeholder' => '$ Amount'
                ]
            ])
		 ->add('rebate', Type\IntegerType::class, [
                'attr' => [
                    'min' => 10,
                    'max' => 100,
                    'class'=>'rebate'
                ],
                'label' => 'Rebate',
            ])
            ->add('amount', Type\TextType::class, [
                'label' => 'Amount you Receive',
                //'data'=> 0,
                'attr' => [
                    'class' => 'wholesale',
                    'data-index' => '__name__',
                    'readonly' => true,
                    'placeholder' => '$'
                ]
            ])
            ->add('subscriptionType', Type\ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'One Week' => 'week',
                    'One Month' => 'month',
                    'Six Month' => 'day',
                    'One Year' => 'year',
                ],
                //'data'=>'week',
                'label' => 'Period',
                'attr' => [
                    'class' => 'subscription-type',
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Price::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_price';
    }
}
