<?php

namespace LocalsBest\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\State;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\AddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class BusinessType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('types', EntityType::class, array(
            'label' => 'Types',
            'class'     => IndustryType::class,
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
            },
            'choice_label'  => 'name',
            'multiple'     => true,
            'required'=> false,
            'placeholder' => 'None',
            'empty_data' => null,
            'attr' => [
                'class' => 'select2_sample1'
            ]
        ));

        $builder->add('name', Type\TextType::class, array(
            'label' => 'Name',
            'required'=> false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Business Name',
                'autofocus' => ''
            )
        ));

        $builder->add('owner', EntityType::class, array(
            'label' => 'Business Owner ',
            'required'=> false,
            'class' => User::class,
            'choice_label' => 'fullName',
            'placeholder' => 'Select User',
            'empty_data' => null,
            'multiple' => false,
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('u')
                    ->innerJoin('u.role', 'r')
                    ->where('r.level <= :manager')
//                    ->andWhere('u.owner IS NULL')
                    ->orderBy('u.firstName', 'ASC')
                    ->addOrderBy('u.lastName', 'ASC')
                    ->setParameter(':manager', 4);
            },
        ));

        $builder->add('contactName', Type\TextType::class, array(
            'label' => 'Contact Name',
            'required'=> false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Contact Name',
                'autofocus' => ''
            )
        ));

        $builder->add('workingStates', EntityType::class, array(
            'label' => 'Working States ',
            'class' => State::class,
            'choice_label' => 'name',
            'required'=> false,
            'placeholder' => 'None',
            'empty_data' => null,
            'multiple' => true,
            'attr' => array(
                'class' => 'form-control select2_sample1',
            )
        ));

        $builder->add('themeColor', Type\ChoiceType::class, array(
            'required'=> false,
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
        ));

        $builder->add('file', VichFileType::class, array(
            'required'      => false,
            'allow_delete'  => false,
            'label'         => false,
        ));

        $builder->add('address', AddressType::class, [
            'label' => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Business::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_business';
    }
}
