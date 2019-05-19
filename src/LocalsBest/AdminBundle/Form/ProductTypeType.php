<?php

namespace LocalsBest\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Product;
use LocalsBest\UserBundle\Entity\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('p')
                        ->where('p.created_by = :user')
                        ->setParameter('user', $user)
                        ->orderBy('p.title', 'ASC');
                },
            ])
            ->add('type', Type\ChoiceType::class, [
                'choices' => [
                    'Counter' => 'counter',
                    'Item' => 'item',
                    'Subscription' => 'subscription'
                ],
               // 'choices_as_values' => false,
            ])
            ->add('price')
            ->add('setupFee')
            ->add('margin', Type\IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ]
            ])
            ->add('value', Type\IntegerType::class, [
                'label' => 'Quantity for Sale',
                'attr' => [
                    'min' => 0,
                ]
            ])
            ->add('subscriptionPeriod')
            ->add('subscriptionCharges')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProductType::class,
            'user' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_product';
    }
}
