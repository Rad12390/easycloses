<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\CommonBundle\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', EntityType::class, [
            'class'     => Status::class,
            'choice_label' => function ($item) {
                return ucfirst($item->getStatus());
            },
            'placeholder' => 'Select',
            'empty_data' => null,
            'attr' => array(
                'class' => 'form-control placeholder-no-fix status_change',
             )
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'statusType';
    }
}
