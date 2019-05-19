<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\AboutMe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutMeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('aboutMe', Type\TextareaType::class, array(
            'label' => 'About me :',
            'required' => false,
            'attr' => array(
                'class'=> 'wysihtml5 form-control',
                'rows' => '6',
                'placeholder' => 'About Me'
            )
        ));
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AboutMe::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'aboutMe';
    }
}
