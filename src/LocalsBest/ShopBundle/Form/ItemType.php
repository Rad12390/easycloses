<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\SocialMediaBundle\Entity\Bucket;
use LocalsBest\UserBundle\Dbal\Types\IndustryType;
use LocalsBest\UserBundle\Entity\Plugin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['user'];
        $industryType = $options['industryType'];
        $respa_industry = IndustryType::$choices;
        $statuses = [
            1 => 'Draft',
            3 => 'Save',
            4 => 'Publish'
        ];

        if ($options['isApproved'] === true) {
            $statuses[2] = 'Approved';
        }

        //check if the login user industry is RESPA Industry
        $ifrespa=0;
        foreach($industryType as $industry){
           if(in_array($industry, $respa_industry)){
               $ifrespa=1;
           }
        }

        if(!$ifrespa){
            $types = [
            1 => 'A Service for Sale',
            3 => 'Info Link by Text or Email',
            //4 => 'Social Media for Sale',
            ];

            if ($user !== null && $user->getRole()->getLevel() == 1) {

                $types[2] = 'Software Upgrade';
            }

            if ($user !== null && $user->getRole()->getLevel() == 4) {
                $status= $user->getCustomQuotes();
                if($status)
                    $types[5] = 'Custom Quote';
            }
        }
        else{
            $types = [
             //   4 => 'Social Media for Sale',
                3 => 'Info Link by Text or Email'
            ];
        }

        $builder
            ->add('title')
            ->add('shortDescription', Type\TextType::class, [
                'required' => false,
                'label' => 'Short Description',
            ])
            ->add('description', Type\TextareaType::class, [
                'label' => 'Long Description',
            ])
            ->add('emailToClient', Type\CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('emailToVendor', Type\CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('textToClient', Type\CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('textToVendor', Type\CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('notificationToClient', Type\CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('notificationToVendor', Type\CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('quantity', Type\IntegerType::class, [
                'label' => 'Inventory',
                'attr' => [
                    'min' => 1,
                ]
            ]);

        if ($user !== null && $user->getRole()->getLevel() == 1) {
            $builder->add('markup');
        }

        $builder
            ->add('type', Type\ChoiceType::class, [
                'label' => 'What is going to the Store?',
                'choices' => array_flip($types),
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('externalLink', Type\TextType::class, [
                'required' => false,
            ])
            ->add('plugin', EntityType::class, [
                'placeholder' => '-Select App Part-',
                'empty_data' => null,
                'required' => false,
                'class' => Plugin::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
            ])
           
            ->add('restrictions', Type\CollectionType::class, [
                'entry_type' => RestrictionType::class,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                    'business' => $options['business'],
                    'states' => $options['states']
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('dispositions', Type\CollectionType::class, [
                'entry_type' => DispositionType::class,
                'label' => false,
                'entry_options'      => array('label' => false),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('images', Type\CollectionType::class, [
                'entry_type' => ImageType::class,
                'label' => false,
                'entry_options'      => array('label' => false),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Item::class,
            'isApproved' => false,
            'user' => null,
            'business' => null,
            'states'=> null,
            'industryType' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_item';
    }
}
