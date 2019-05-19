<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\DocumentJob;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('file', Type\FileType::class, [
            'attr' => [
                'accept' => 'image/jpeg,image/gif,image/png'
            ]]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentJob::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'document';
    }
}
