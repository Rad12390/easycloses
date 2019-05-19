<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShareType extends AbstractType
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $user = $this->user;
        
        $builder->add('user', EntityType::class, [
            'class' => User::class,
            'required' => true,
            'choice_label' => 'username',
            'placeholder' => '- Select a User - ',
            'empty_data' => null,
            'attr' => array(
                'class' => 'form-control select2_category',
            ),
            'query_builder' => function( $er ) use ($user){
                return $er  ->createQueryBuilder('u')
                            ->where('u.id != ?1')
                            ->join('u.role', 'r')
                            ->andwhere('r.level > ?2')
                            ->setParameter(1, $user->getId())
                            ->setParameter(2, $user->getRole()->getLevel());
                  
            }
        ]);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Share::class
        ));
    }
    
    public function getBlockPrefix()
    {
        return 'share';
    }
}
