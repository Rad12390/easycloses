<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Language;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class AccountProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder->add('username', Type\TextType::class, [
            'required' => false,
            'attr' => array(
                'placeholder' => 'Username:',
            )
        ]);

        $builder->add('aboutMe', Type\TextareaType::class, [
            'label' => 'About Me:',
            'required' => false,
            'attr' => array(
                'class'=> 'wysihtml5',
                'rows' => '6',
                'placeholder' => 'About Me'
            )
        ]);
        
        $builder->add('dba', Type\TextType::class, [
            'label' => 'DBA:',
            'required' => false,
            'attr' => array(
                'placeholder' => 'Doing Business As',
            )
        ]);

        $builder->add('languages', EntityType::class, [
            'label' => 'Languages Spoken:',
            'class' => Language::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('l')
                    ->select("l, FIELD(l.language, 'English') as HIDDEN field")
                    ->orderBy('field', 'DESC')
                    ->addOrderBy('l.language', 'ASC');
            },
            'required' => false,
            'choice_label' => 'language',
            'placeholder' => 'None',
            'empty_data' => null,
            'multiple' => true,
            'attr' => array(
                'class' => 'select2',
            )
        ]);

        $builder->add('birthday', Type\DateTimeType::class, array(
            'attr' => array(
                'placeholder' => 'Birthday',
            ),
            'label' => 'Birthday',
            'widget' => "single_text",
            'format' => 'MM/dd/yyyy',
            'required' => false,
        ));

        $builder->add('file', VichFileType::class, array(
            'attr'          => array(
                'accept' => 'image/jpeg,image/gif,image/png'
            ),
            'required'      => false,
            'allow_delete'  => false, // not mandatory, default is true
            'download_link' => true, // not mandatory, default is true
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'accountProfile';
    }
}
