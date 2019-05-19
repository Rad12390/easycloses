<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class BusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class, [
            'label' => 'Name',
            'required' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Business Name',
                'autofocus' => ''
            )
        ]);

        $builder->add('themeColor', Type\ChoiceType::class, [
            'required' => false,
            'choices' => array(
                'Default' => 'default',
                'Blue' => 'blue',
                'Dark Blue' => 'darkblue',
                'Purple' => 'purple',
                'Grey' => 'grey',
                'Light' => 'light',
                'Light 2' => 'light2',
            ),
            'empty_data' => 'light',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);

        $builder->add('rating', Type\TextType::class, [
            'label' => 'Rating',
            'required'=> false,
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

        $builder->add('workingStates', EntityType::class, [
            'label' => 'Working States ',
            'class' => State::class,
            'choice_label' => 'name',
            'required'=> false,
            'placeholder' => 'None',
            'empty_data' => null,
            'multiple' => true,
            'attr' => array(
                'class' => 'form-control select2',
            )
        ]);

        $builder->add('aboutMe', Type\CollectionType::class, [
            'label' => false,
            'entry_type'         => AboutMeType::class,
            'allow_add'    => true,
            'required' => false,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'entry_options' => array(
                // options on the rendered TagTypes
            ),
        ]);

        $builder->add('file', VichFileType::class, [
            'attr'          => array(
                'accept' => 'image/jpeg,image/gif,image/png'
            ),
            'required'      => false,
            'allow_delete'  => false, // not mandatory, default is true
            'download_link' => true, // not mandatory, default is true
        ]);

        $builder->add('address', BusinessAddressType::class);

        $builder->add('types', EntityType::class, [
            'label' => 'Types',
            'class'     => IndustryType::class,
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
            },
            'choice_label'  => 'name',
            'required'=> false,
            'multiple'     => true,
            'placeholder' => 'None',
            'empty_data' => null,
            'attr' => array(
                'class' => 'form-control select2',
             )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Business::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'business';
    }
}
