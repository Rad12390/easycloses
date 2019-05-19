<?php

namespace LocalsBest\ShopBundle\Form;

use LocalsBest\ShopBundle\Entity\Combo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComboType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $business = $options['business'];
        $combo = $options['combo'];

        $statuses = [
            1 => 'Draft',
            3 => 'Archived',
            4 => 'Published'
        ];

        if ($options['isApproved'] === true) {
            $statuses[2] = 'Approved';
        }

        $builder
            ->add('title', Type\TextType::class, [
                'required' => false,
            ])
            ->add('quantity', Type\IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('status', Type\ChoiceType::class, [
                'choices' => array_flip($statuses)
            ])
            ->add('skuSets', Type\CollectionType::class, [
                'entry_type' => ComboSkuSetType::class,
                'label' => false,
                'entry_options'      => [
                    'label' => false,
                    'user' => $user,
                    'skuId' => $combo->getSku() !== null ? $combo->getSku()->getId() : null,
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('sku', SkuType::class, [
                'label' => false,
                'user' => $user,
                'business' => $business,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Combo::class,
            'user' => null,
            'business' => null,
            'isApproved' => false,
            'combo' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_combo';
    }
}
