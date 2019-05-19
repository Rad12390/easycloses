<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\ContactUs;
use LocalsBest\UserBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class createPdfTemplates extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'required' => false,
                'class' => Product::class,
                'choice_label' => 'title',
                'placeholder' => '-Select Product-',
                'empty_data' => null,
            ])
            ->add('userName', Type\TextType::class, [
                'label' => 'Full Name',
            ])
            ->add('email')
            ->add('phone')
            ->add('industryType', Type\TextType::class, [
                'required' => false,
            ])
            ->add('mlsBoard', Type\TextType::class, [
                'label' => 'MLS Board',
                'required' => false,
            ])
            ->add('note')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactUs::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_contactus';
    }
}
