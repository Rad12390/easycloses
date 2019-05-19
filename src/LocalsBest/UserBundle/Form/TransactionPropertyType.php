<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\TransactionProperty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('property', PropertyType::class);
        
        $builder->add('mlsNumber', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'MLS #',
                'autofocus' => ''
            )
        ]);
        $builder->add('mlsBoard', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Unit #',
                'autofocus' => ''
            )
        ]);
        $builder->add('yearBuilt', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Year Built',
                'autofocus' => ''
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransactionProperty::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'transactionProperty';
    }
}
