<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $business = $options['business'];

        $types = [
            'Regular_Sale' => 'Regular Sale',
            'Bank_Sale_or_REO' => 'Bank Sale or REO',
            'New_Construction' => 'New Construction',
            'Short_Sale' => 'Short Sale',
            'Lease' => 'Lease',
        ];

        if ($business !== null && $business->getId() == 19) {
            $types['Facilitator'] = 'Facilitator';
        }

        $builder->add('type', Type\ChoiceType::class, [
            'required' => true,
            'choices' => array_flip($types),
            'placeholder' => 'Transaction Type',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix required_field required',
            )
        ]);
        $builder->add('represent', Type\ChoiceType::class, [
            'required' => true,
            'choices' => array('Seller' => 'Seller', 'Landlord' => 'Landlord'),
            'placeholder' => 'Client Type',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix required_field required',
            )
        ]);
        $builder->add('status', Type\ChoiceType::class, [
            'choices' => array(
                'Active' => 'Active',
                'Withdrawn' => 'Withdrawn',
                'Expired' => 'Expired',
                'Temporarily Off Market' =>'Temporarily_Off_Market',
            ),
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
        $builder->add('sellerContact', TransactionContactType::class);
        $builder->add('sellerContact2', TransactionContactType::class);
        $builder->add('listingAgentContact', TransactionContactType::class);
        $builder->add('listingOfficeContact', TransactionContactType::class);
        $builder->add('titleCompanyContact', TransactionContactType::class);
        
        
        $builder->add('moneyBox', ListingMoneyBoxType::class);
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
        $builder->add('agentCommissions', Type\CollectionType::class, [
            'entry_type' => CommissionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
        $builder->add('listingContacts', Type\CollectionType::class, [
            'entry_type' => TransactionEventType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
        $builder->add('transactionNote', TransactionNoteType::class);
        $builder->add('transactionShowing', TransactionNoteType::class);
        $builder->add('advancedCommissionBox', Type\HiddenType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Listing::class,
            'business' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'listing';
    }
}
