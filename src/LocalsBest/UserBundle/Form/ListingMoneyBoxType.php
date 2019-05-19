<?php

namespace LocalsBest\UserBundle\Form;

use Exception;
use LocalsBest\UserBundle\Entity\ListingMoneyBox;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingMoneyBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contractPrice', Type\TextType::class, [
            'required' => true,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix input-lg currency required_field required',
                'placeholder' => '$ Amount',
                'autofocus' => ''
            )
        ]);
        $builder->add('referral', Type\ChoiceType::class, [
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
            },
            'expanded' => true,
            'placeholder' => false
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ListingMoneyBox::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'listingMoneyType';
    }
}
