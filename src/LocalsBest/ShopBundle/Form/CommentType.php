<?php

namespace LocalsBest\ShopBundle\Form;

use LocalsBest\ShopBundle\Entity\Comment;
use LocalsBest\ShopBundle\Entity\Item;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sku = $options['sku'];

        $builder
            ->add('item', EntityType::class, [
                'required' => true,
                'class' => Item::class,
                'choices' => $sku->getPrintableItems(),
            ])
            ->add('text', Type\TextareaType::class, [
                'label' => 'Review',
                'attr' => [
                    'rows' => 12,
                ],
            ])
            ->add('rating', Type\NumberType::class, [
                'data' => 5,
                'attr' => [
                    'class' => 'kv-fa',
                    'data-size' => "sm",

                ]
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn-primary'
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class,
            'sku' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_comment';
    }
}
