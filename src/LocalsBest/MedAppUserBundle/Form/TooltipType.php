<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Tooltip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TooltipType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tooltips = $options['tooltips'];
        
        $result=[];
        foreach ($tooltips as $tooltip) {
            $result[$tooltip->getName()] = $tooltip->getName();
        }
        
        foreach ($tooltips as $tooltip) {
            if ($options['currentValue'] !== null && $options['currentValue'] == $tooltip->getName()) {
                continue;
            }

            if ($key = array_search($tooltip->getName(), $result)) {
                unset($result[$key]);
            }
        }

        $builder
            ->add('name', Type\TextType::class, [
                'required' => false,
            ])
            ->add('body', Type\TextareaType::class, [
                'label' => 'Text',
                'required' => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tooltip::class,
            'tooltips' => [],
            'currentValue' => null,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_tooltip';
    }
}
