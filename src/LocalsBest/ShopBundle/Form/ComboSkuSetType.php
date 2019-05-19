<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\ComboSkuSet;
use LocalsBest\ShopBundle\Entity\Sku;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComboSkuSetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $skuId = $options['skuId'];

        $builder
            ->add('quantity', Type\IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('sku', EntityType::class, [
                'class' => Sku::class,
                'choice_label' => 'title',
                'query_builder' => function(EntityRepository $repository) use ($user, $skuId) {
                    return $repository->getForForm($user, $skuId);
                },
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ComboSkuSet::class,
            'user' => null,
            'skuId' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_comboskuset';
    }
}
