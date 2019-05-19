<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['data']->getId(); // currently edited object

        $builder
            ->add('title')
            ->add('parent', EntityType::class, [
                'placeholder' => 'Select Parent Category',
                'empty_data' => null,
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'title',
                'query_builder' => function(EntityRepository $er) use ($id) {
                    $query = $er->createQueryBuilder('c');
                    if ($id !== null) {
                        $query->where('c.id <> :id')->setParameter('id', $id);
                    }
                    return $query;
                }
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_category';
    }
}
