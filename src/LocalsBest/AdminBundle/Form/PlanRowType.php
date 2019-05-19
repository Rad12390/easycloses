<?php

namespace LocalsBest\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use LocalsBest\UserBundle\Entity\PlanRow;

class PlanRowType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('industryGroup', Type\TextType::class, [
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('setupGoldPrice')
            ->add('goldPrice')
            ->add('setupSilverPrice')
            ->add('silverPrice')
            ->add('setupBronzePrice')
            ->add('bronzePrice')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PlanRow::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_adminbundle_planrow';
    }
}
