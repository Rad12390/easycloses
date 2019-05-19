<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\ItemSet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemSetType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $user = $options['user'];
        $states= $options['states'];
        $quote= $options['quote'];
       
        
        $builder
            ->add('quantity', Type\IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'class' => 'quantityClass'
		   
                ],
		// 'data'=> 1
            ])
                 
            ->add('usesLimit', Type\ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Unlimited' => -1,
                    'One' => 1,
                    'Five' => 5,
                ],
                'empty_data' => null,
                'placeholder' => '-- Limits for plugin uses --',
            ])
//            ->add('isPrintable', Type\HiddenType::class, [
//                'required' => false,
//                'data'=>true
//                
//           ])
            ->add('item', EntityType::class, [
                'class' => Item::class,
                'choice_label' => 'title',
                'placeholder' => '-Select-',
                'attr' => array(
                    'class' => 'item-select'
                ),
                'label'=> 'Item Name',
                'query_builder' => function(EntityRepository $repository) use ($user, $quote) {
                   
                    if($quote == null){
                        $result = $repository
                            ->createQueryBuilder('i')
                            ->where('i.createdBy = :user')
                            ->andWhere('i.type != :type')
                            ->andWhere('i.status = :ableStatus')
                            ->setParameter('user', $user)
                            ->setParameter('type', 5)
                            ->setParameter('ableStatus', Item::STATUS_APPROVED)
                            ->orderBy('i.title', 'ASC')
                        ;
                    }
                    else{
                        $result = $repository
                            ->createQueryBuilder('i')
                            ->where('i.createdBy = :user')
                            ->andWhere('i.status = :ableStatus')
                            ->setParameter('user', $user)
                            ->setParameter('ableStatus', Item::STATUS_APPROVED)
                            ->orderBy('i.title', 'ASC')
                        ;
                    }
                    return $result;
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
            'data_class' => ItemSet::class,
            'user' => null,
            'states' =>null,
            'quote' => null,
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_itemset';
    }
}
