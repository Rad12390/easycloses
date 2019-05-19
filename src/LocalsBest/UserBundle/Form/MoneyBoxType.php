<?php

namespace LocalsBest\UserBundle\Form;

use Exception;
use LocalsBest\UserBundle\Entity\MoneyBox;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('deposits', Type\CollectionType::class, [
            'entry_type' => DepositType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
        $builder->add('contractPrice', Type\TextType::class, [
            'required' => true,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix input-lg currency required_field required',
                'placeholder' => '$ Amount',
                'autofocus' => ''
            )
        ]);
        $builder->add('paymentType', Type\ChoiceType::class, [
            'label' => 'Payment Type',
            'required' => false,
            'choices' => array(
                'Cash' => 'Cash',
                'Loan' => 'Loan',
                '1031 exchange' => '1031 exchange',
                'Selling a property' =>'Selling a property',
            ),
            'placeholder' => 'Choose One',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
        $builder->add('loan', Type\ChoiceType::class, [
                'required' => false,
                'attr' => array(
                'class' => 'required_field required loanOption',
                ),
                'choices'  => array( 
                    'Yes' => true,
                    'No' => false
            ),
            'choice_value' => function ($choiceKey) {
                if (null === $choiceKey) {
                    return null;
                }

                // cast to string after testing for null,
                // as both null and false cast to an empty string
                $stringChoiceKey = (string) $choiceKey;

                // true casts to '1'
                if ('1' == $stringChoiceKey) {
                    return 'true';
                }

                // false casts to an empty string
                if ('' === $stringChoiceKey || '0' === $stringChoiceKey) {
                    return 'false';
                }

                throw new Exception('Unexpected choice key: ' . $choiceKey);
            },
            'expanded' => true, 
            'placeholder' => false
        ]);

        $builder->add('referral', Type\ChoiceType::class, [
                'label'    => 'Do you owe a referral fee',
                'required' => false,
                'attr' => array(
                'class' => 'required_field',
                ),
                'choices'  => array( 
                    'Yes' => true,
                    'No' => false
                ),
            'choice_value' => function ($choiceKey) {
                if (null === $choiceKey) {
                    return null;
                }

                // cast to string after testing for null,
                // as both null and false cast to an empty string
                $stringChoiceKey = (string) $choiceKey;

                // true casts to '1'
                if ('1' == $stringChoiceKey) {
                    return 'true';
                }

                // false casts to an empty string
                if ('' === $stringChoiceKey || '0' === $stringChoiceKey) {
                    return 'false';
                }

                throw new Exception('Unexpected choice key: ' . $choiceKey);
            },            'expanded' => true,
            'placeholder' => false
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MoneyBox::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'moneyType';
    }
}
