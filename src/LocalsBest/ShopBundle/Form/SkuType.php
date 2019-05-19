<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\Category;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkuType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $states= $options['states'];
        
        $builder
            ->add('prices', Type\CollectionType::class, [
                'entry_type' => PriceType::class,
                'label' => false,
                'entry_options'      => array('label' => false),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'label'=> 'Shop Display Tab'
            ])
        ;

        if ($user->getRole()->getLevel() == 1) {
            $builder->add('adminRestrictions', Type\CollectionType::class, [
                'entry_type' => RestrictionType::class,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                    'business' => $options['business'],
                    'states' => $states
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Sku::class,
            'user' => null,
            'business' => null,
            'states' => null
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_sku';
    }
}
