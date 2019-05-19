<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Link;
use LocalsBest\UserBundle\Entity\LinkGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('source')
            ->add('title')
            ->add('group', EntityType::class, [
                'required' => false,
                'placeholder' => false,
                'label'     => 'Link Group',
                'choice_label'  => 'title',
                'class'     => LinkGroup::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    if ($user->getRole()->getLevel() <= 5) {
                        return $er->createQueryBuilder('lg');
                    } else {
                        return $er->createQueryBuilder('lg')
                            ->where('lg.id <> :id')
                            ->setParameter('id', 3)
                            ;
                    }
                },
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Link::class,
            'user' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_link';
    }
}
