<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChampionSettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('champion_bin_size', Type\IntegerType::class, [
                'label' => 'Size of the Bin',
                'required' => false,
            ])
            ->add('champion_frequency', Type\IntegerType::class, [
                'label' => 'Frequency',
                'required' => false,
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_championsettings';
    }
}
