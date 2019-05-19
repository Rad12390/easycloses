<?php

namespace LocalsBest\UserBundle\Form;

use Exception;
use LocalsBest\UserBundle\Entity\Closing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClosingType extends AbstractType
{
    const MODE_BUYER    = 'B';
    const MODE_TENANT   = 'T';
    const MODE_NORMAL   = 'N';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $business = $options['business'];
        $mode = $options['mode'];

        $choices = [
            'Regular_Sale' => 'Regular Sale', 
            'Bank_Sale_or_REO' => 'Bank Sale or REO',
            'New_Construction' => 'New Construction',
            'Short_Sale' => 'Short Sale',
            'Lease' => 'Lease'
        ];

        if ($business !== null && $business->getId() == 19) {
            $choices['Facilitator'] = 'Facilitator';
        }

        if ($mode === Closing::MODE_TENANT) {
            $choices = [
                'Lease' => 'Lease'
            ];
        }
        
        $builder->add('type', Type\ChoiceType::class, [
            'choices' => array_flip($choices),
            'placeholder' => 'Transaction Type',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix required_field required',
            )
        ]);
        
        $builder->add('represent', Type\ChoiceType::class, [
            'choices' => array('Buyer' => 'Buyer', 'Tenant' => 'Tenant'),
            'placeholder' => 'Client Type',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix required_field required',
                'autofocus' => ''
            )
        ]);
        $builder->add('status', Type\ChoiceType::class, [
            'choices' => array(
                'Pending' => 'Pending',
                'Active with Contract' => 'Active_with_Contract',
                'Contract fell thru' => 'Contract_fell_thru',
                'Leased not Paid' => 'Leased_not_Paid',
                'Leased Paid' => 'Leased_Paid',
                'Sold not Paid' => 'Sold_not_Paid',
                'Sold Paid' => 'Sold_Paid',
                'Rejected Offer' => 'Rejected_Offer'
            ),
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
        $builder->add('buyerContact', TransactionContactType::class);
        $builder->add('buyerContact2', TransactionContactType::class);
        $builder->add('buyersAgentContact', TransactionContactType::class);
        $builder->add('buyersOfficeContact', TransactionContactType::class);
        $builder->add('sellerContact', TransactionContactType::class);
        $builder->add('listingAgentContact', TransactionContactType::class);
        $builder->add('listingOfficeContact', TransactionContactType::class);
        $builder->add('titleCompanyContact', TransactionContactType::class);
        $builder->add('escrowCompanyContact', TransactionContactType::class);
		
		/*** added for   company  seller Attorney    ***/
        $builder->add('sellerattorneyCompanyContact', TransactionContactType::class);
		/*** added for   company  seller Attorney ***/
		
        $builder->add('homeInspectorContact', TransactionContactType::class);
        $builder->add('homeInsuranceContact', TransactionContactType::class);
        $builder->add('lenderContact', TransactionContactType::class);
        
        
        $builder->add('hoa', Type\ChoiceType::class, array(
            'required' => false,
            'attr' => [
                'class' => 'required_field required hoaOption',
            ],
            'choices'  => [
                'No' => false,
                'Yes' => true,
            ],
//            'choices_as_values' => true,
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
            'multiple'  => false,
            'expanded' => true,
            'placeholder' => false
        ));

        $builder->add('moneyBox', MoneyBoxType::class);

        $builder->add('referrals', Type\CollectionType::class, [
            'entry_type' => ReferralType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);

        $builder->add('totalCommissions', Type\CollectionType::class, [
            'entry_type' => CommissionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
        $builder->add('buyerAgentCommissions', Type\CollectionType::class, [
            'entry_type' => CommissionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);

        $builder->add('closingContacts', Type\CollectionType::class, [
            'entry_type' => TransactionEventType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);

        $builder->add('transactionNote', TransactionNoteType::class);

        $builder->add('advancedCommissionBox', Type\HiddenType::class);
        
        if ($mode === self::MODE_BUYER) {
            $builder->remove('represent')
                ->add('represent', Type\ChoiceType::class, [
                    'choices' => array('Seller' => 'Seller', 'Buyer&Seller' => 'Seller&Buyer'),
                    'placeholder' => 'Client Type',
                    'attr' => array(
                        'class' => 'form-control placeholder-no-fix required_field required',
                        'autofocus' => ''
                    )
                ]);
        } elseif ($mode === self::MODE_TENANT) {
            $builder->remove('represent')
            ->add('represent', Type\ChoiceType::class, [
                'choices' => array('Landlord' => 'Landlord', 'Landlord &Tenant' => 'Landlord&Tenant'),
                'placeholder' => 'Client Type',
                'attr' => array(
                    'class' => 'form-control placeholder-no-fix required_field required',
                    'autofocus' => ''
                )
            ]);
        } elseif ($mode === self::MODE_NORMAL) {
            $builder->remove('represent')
            ->add('represent', Type\ChoiceType::class, [
                'choices' => array('Buyer' => 'Buyer', 'Tenant' => 'Tenant'),
                'placeholder' => 'Client Type',
                'attr' => array(
                    'class' => 'form-control placeholder-no-fix required_field required',
                    'autofocus' => ''
                )
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Closing::class,
            'business' => null,
            'mode' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'closing';
    }
}
