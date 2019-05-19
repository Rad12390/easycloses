<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\CommonBundle\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('note', Type\TextareaType::class, [
            'label' => 'Note :',
            'required' => false,
            'attr' => array(
                'class'=> 'form-control',
                'rows' => '3',
                'placeholder' => ''
            )
        ]);
        $builder->add('private', Type\CheckboxType::class, [
            'label'     => 'Mark as a Private Note',
            'required'  => false,
        ]);
        $builder->add('important', Type\CheckboxType::class, [
            'label'     => 'Mark as a Important Note',
            'required'  => false,
        ]);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'add_note';
    }
}
