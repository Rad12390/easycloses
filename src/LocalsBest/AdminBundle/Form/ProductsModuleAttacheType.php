<?php

namespace LocalsBest\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\ProductsModule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsModuleAttacheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('businesses', EntityType::class, array(
            'label' => 'Businesses',
            'class'     => Business::class,
            'query_builder' => function(EntityRepository $repository) {
                return $repository
                    ->createQueryBuilder('b')
                    ->orderBy('b.name', 'ASC')
                ;
            },
            'choice_label' => 'name',
            'multiple'     => true,
            'required'=> false,
            'placeholder' => 'None',
            'empty_data' => null,
            'attr' => [
                'class' => 'select2'
            ]
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProductsModule::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_business';
    }
}
