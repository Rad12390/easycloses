<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\PaymentSplitSettings;
use LocalsBest\UserBundle\Entity\Business;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentSplitSettingsType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $role= $options['user']->getRole()->getRole();
        
        $role_array=["ROLE_ASSIST_MANAGER","ROLE_AGENT","ROLE_CLIENT"];
        $builder
                
                
               // ->add('employeePercentage', 'integer')
                ->add('charities', EntityType::class, [
                    'label' => 'Charity Selection',
                    'class' => Business::class,
                    'query_builder' => function(EntityRepository $repository) {
                        return $repository->getCharities();
                    },
                    'attr' => [
                        'class' => 'select2',
                    ],
                    'choice_label' => 'name',
                    'placeholder' => 'None',
                    'empty_data' => null,
                    'label_attr'=> ['class'=> 'colorblack'],
                    'multiple' => true
                ]);

            if(!in_array($role,$role_array)){
                //  fields for "use the charity picked by"  
                $builder
                ->add('targettwo', Type\ChoiceType::class, [
                    'multiple' => false,
                    'label' => 'Use the charity picked by ',
                    'expanded' => true,
                    'choices' => [
                        'Me' => 'user',
                        'Employee' => 'employee'
                    ],
                    'label_attr'=> ['class'=> 'colorblack']
                ])
                ->add('charityPercentage', Type\IntegerType::class, [
                    'label_attr'=> ['class'=> 'colorblack']
                ])
                ->add('managerEmployeePercentage', Type\IntegerType::class, [
                    'label' => 'Employee percentage',
                    'label_attr'=> ['class'=> 'colorblack']
                ])
                ->add('businessPercentage', Type\IntegerType::class, [
                    'label' => 'Mine/business percentage',
                    'label_attr'=> ['class'=> 'colorblack']
                ])
                
                ->add('target', Type\ChoiceType::class, [
                    'multiple' => false,
                    'label' => 'Send payment to',
                    'expanded' => true,
                    'choices' => [
                        'Me' => 'user',
                        'Business' => "business"
                    ],
                    'label_attr'=> ['class'=> 'colorblack']
                ]);
            }
            if($role!='ROLE_MANAGER'){
                $builder
                ->add('rebateStatus', Type\ChoiceType::class, [
                    'multiple' => false,
                    'label' => 'Send Rebate To',
                    'expanded' => true,
                    'choices' => [
                        "Send all eligible rebates to Me" => 'me',
                        "Send all eligible rebates to Charity" => 'charity',
                    ],
                    'label_attr'=> ['class'=> 'colorblack']
                ]);
            }
            $builder->add('submit', Type\SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PaymentSplitSettings::class,
            'user'=> NULL,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_paymentsplitsettings';
    }
}
