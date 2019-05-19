<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('transactionProperty', TransactionPropertyType::class);
        
        $builder->add('listing', ListingType::class, ['business' => $options['business']]);
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'business' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'transactionListing';
    }
}
