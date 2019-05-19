<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\Vendor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminVendorType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('businessName', Type\TextType::class, [
            'label' => 'Business Name',
            'required'=> false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Business Name',
                'autofocus' => ''
            )
        ]);
        
        $builder->add('contactName', Type\TextType::class, [
            'label' => 'Contact Name',
            'required'=> false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Contact Name',
                'autofocus' => ''
            )
        ]);

        $builder->add('businessType', EntityType::class, [
            'label' => 'Business Category',
            'class'     => IndustryType::class,
            'query_builder' => function(EntityRepository $repository) { 
                return $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
            },
            'choice_label'  => 'name',
            'placeholder' => 'Select',
            'empty_data' => null,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
             )
        ]);
        $builder->add('email', Type\EmailType::class, [
            'label' => 'Contact Email',
            'required'=> true,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix add_email required',
                'placeholder' => 'Contact Email',
                'autofocus' => ''
            )
        ]);
        $builder->add('number', Type\TextType::class, [
            'label' => 'Contact Phone',
            'required'=> false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Contact Phone',
                'autofocus' => ''
            )
        ]);
        $builder->add('category', Type\ChoiceType::class, [
            'required' => true,
            'choices' => array(
                'Gold' => 3,
                'Silver' => 2,
                'bronze' => 1,
                'free' => 0,
                ),
            'placeholder' => 'Choose One',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix required_field required',
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vendor::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'vendor';
    }
}
