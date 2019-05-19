<?php

namespace LocalsBest\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\ProductsModule;
use LocalsBest\UserBundle\Entity\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsModuleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class, array(
            'label' => 'Name',
            'required'=> false,
            'attr' => array(
                'placeholder' => 'Module Name',
            )
        ));

        $builder->add('description', Type\TextareaType::class, [
            'label' => 'Description',
            'required'=> false,
            'attr' => array(
                'placeholder' => 'Module Description',
            )
        ]);

        $builder->add('productTypes', EntityType::class, array(
            'label' => 'Product Types',
            'class'     => ProductType::class,
            'query_builder' => function(EntityRepository $repository) {
                return $repository
                    ->createQueryBuilder('pt')
                    ->join('pt.product', 'p')
                    ->orderBy('p.title', 'ASC')
                ;
            },
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
