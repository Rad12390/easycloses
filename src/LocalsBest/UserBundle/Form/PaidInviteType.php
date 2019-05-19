<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\PaidInvite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaidInviteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('industryType', EntityType::class, [
                'required' => false,
                'label' => 'Service Category',
                'class'     => IndustryType::class,
                'query_builder' => function(EntityRepository $repository) use ($user) {
                    return $repository->getActive($user);
                },
                'choice_label'  => 'name',
                'placeholder' => 'Select Type',
                'empty_data' => null,
            ])
            ->add('recipient', Type\EmailType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Email Address',
                ]
            ])
            ->add('phone_number', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Phone Number',
                ]
            ])
            ->add('business_name', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Business Name',
                ]
            ])
            ->add('contact_name', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Contact Name',
                ]
            ])
            ->add('save', Type\SubmitType::class, [
                'label' => 'Invite',
                'attr' => array('class' => 'btn-success'),
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaidInvite::class,
            'user' => null,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_paidinvite';
    }
}
