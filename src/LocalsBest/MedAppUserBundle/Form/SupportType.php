<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Support;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', Type\ChoiceType::class, [
            'required' => false,
            'choices' => array(
                'Transactions' => 'Transactions', 
                'Activities' => 'Activities',
                'Contacts' => 'Contacts',
                'Leads' => 'Leads',
                'Concierge' => 'Concierge',
                'General Issue' => 'General-Issue'
                ),
            'data' => 'General-Issue',
            'placeholder' => false,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
        $builder->add('note', Type\TextareaType::class, [
            'label' => 'Note :',
            'required' => false,
            'attr' => array(
                'class'=> 'form-control',
                'rows' => '6',
                'placeholder' => ''
            )
        ]);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Support::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'suport';
    }
}
