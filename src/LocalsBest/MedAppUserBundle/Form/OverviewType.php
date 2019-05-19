<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\City;
use LocalsBest\UserBundle\Entity\CityRepository;
use LocalsBest\UserBundle\Entity\Email;
use LocalsBest\UserBundle\Entity\Phone;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OverviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->user = $options['user'];
        $contact = $this->user->getContact();
        
        $builder
            ->add('firstName', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'First Name',
                )
            ])
            ->add('lastName', Type\TextType::class, [
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Last Name',
                )
            ])
            ->add('primaryEmail', EntityType::class, [
                'required' => false,
                'placeholder' => false,
                'class'     => Email::class,
                'choice_label'  => 'email',
                'query_builder' => function( $er ) use ($contact){
                    return $er->createQueryBuilder('e')
                      ->where('e.contact = ?1')
                      ->setParameter(1, $contact);
                }
            ])
            ->add('primaryPhone', EntityType::class, [
                'required' => false,
                'placeholder' => false,
                'class'     => Phone::class,
                'choice_label'  => 'extendedNumber',
                'query_builder' => function( $er ) use ($contact){
                    return $er->createQueryBuilder('e')
                      ->where('e.contact = ?1')
                      ->setParameter(1, $contact);
                }
            ])
            ->add('wp_website_url', Type\TextType::class, [
                'label' => 'WordPress URL:',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control placeholder-no-fix',
                    'placeholder' => 'http://your-wordpress-url.com'
                )
            ])
            ->add('wp_username', Type\TextType::class, [
                'label' => 'WordPress Username:',
                'required' => false,
                'property_path'=>'wp_credentials[wp_username]',
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => "username"
                )
            ])
            ->add('wp_password', Type\TextType::class, [
                'label' => 'WordPress Password:',
                'required' => false,
                'property_path'=>'wp_credentials[wp_password]',
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'password'
                )
            ])
            ->add('workingCities', EntityType::class, [
                'label' => 'Working City ',
                'class' => City::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'None',
                'empty_data' => null,
                'multiple' => true,
                'query_builder' => function (CityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.state in (:states)')
                        ->setParameter(':states', count($this->user->getBusinesses()) == 0 ? [] : $this->user->getBusinesses()->first()->getWorkingStates()->toArray())
                        ->orderBy('c.name', 'ASC');
                },
                'attr' => array(
                    'class' => 'select2',
                )
            ]);
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
        return 'user_overview';
    }
}
