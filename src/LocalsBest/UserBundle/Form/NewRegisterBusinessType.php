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

class NewRegisterBusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class, [
            'label' => 'Name',
            'required' => false,
            'attr' => array(
                'placeholder' => 'Business Name',
            )
        ]);

        $builder->add('contactName', Type\TextType::class, [
            'label' => 'Contact Name',
            'required' => false,
            'attr' => array(
                'placeholder' => 'Contact Name',
            )
        ]);

        $builder->add('types', EntityType::class, [
            'label' => 'Industry Types',
            'required' => false,
            'class'     => IndustryType::class,
            'query_builder' => function(EntityRepository $repository) {
                return $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
            },
            'choice_label'  => 'name',
            'multiple'     => true,
            'placeholder' => 'None',
            'empty_data' => null,
            'attr' => array(
                'class' => 'select2',
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
                'class' => 'select2',
            )
        ]);

        $builder->add('address', BusinessAddressType::class, [
            'required' => false,
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
