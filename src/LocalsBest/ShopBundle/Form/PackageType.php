<?php

namespace LocalsBest\ShopBundle\Form;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use LocalsBest\ShopBundle\Entity\Package;
use LocalsBest\UserBundle\Dbal\Types\IndustryType as IndustryTypeDbal;
use LocalsBest\UserBundle\Entity\IndustryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PackageType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $user = $options['user'];
        $business = $options['business'];
        $industryType = $options['industryType'];
        $states = $options['states'];
        $quote = $options['quote'];
        $respa_industry = IndustryTypeDbal::$choices;
        $statuses = [
            1 => 'Draft',
            3 => 'Archive',
            4 => 'Publish'
        ];

        if ($options['isApproved'] === true) {
            $statuses[2] = 'Approved';
        }
        $types = [];
        //check if the login user industry is RESPA Industry
        $ifrespa=0;
        foreach($industryType as $industry){
           if(in_array($industry, $respa_industry)){
               $ifrespa=1;
           }
        }

        if(!$ifrespa){
            if($quote==NULL){

                $types = [
                    1 => 'A Service for Sale',
                   // 4 => 'Social Media for sale',
                    6 => 'Info Link by Text or Email',
                ];
            }
            if ($user !== null && $user->getRole()->getLevel() == 1 && $quote==NULL) {

                $types[4] = 'Social Media for Sale';
                $types[3] = 'External Link';
                $types[2] = 'Software Upgrade';
                $types[5] = 'Industry Group';
            }

            if ($user !== null && $user->getRole()->getLevel() == 4) {

                $status= $user->getCustomQuotes();
                if($status && $quote!=NULL)
                    $types[7] = 'Custom Quote';
            }
        }
        else{
            $types[4] = 'Social Media for Sale';
            $types[6] = 'Info Link by Text or Email';
        }
        $builder
                ->add('title')
                ->add('type', Type\ChoiceType::class, [
                    'choices' => array_flip($types),
                    'label' => 'What would you like to put in the store?',
                    'expanded' => true,
                ])
                ->add('shortDescription', Type\TextType::class, [
                    'label' => 'Short Description'
                ])
                ->add('description')
                ->add('file', VichFileType::class, [
                    'required' => true,
                    'allow_delete' => false,
                    'label' => false,
                    'attr' => [
                        'accept' => 'image/jpeg, image/gif, image/png'
                    ],
                ])
                ->add('imagename', Type\HiddenType::class)
                ->add('quantity', Type\IntegerType::class, [
                    'attr' => [
                        'min' => 1,
                    ],
                    'label' => 'Quantity For Sale'
                        //     'data'=> 1
                ])
                ->add('industryType', EntityType::class, [
                    'class' => IndustryType::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('i')
                                ->where('i.id IN (:user)')
                                ->setParameter('user', $options['industryType'], Connection::PARAM_INT_ARRAY);
                    },
                    'label' => 'Service Type'
                ])
                ->add('sets', Type\CollectionType::class, [
                    'entry_type' => ItemSetType::class,
                    'label' => false,
                    'entry_options' => array(
                        'label' => false,
                        'user' => $user,
                        'quote'=> $quote
                    ),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('sku', SkuType::class, [
                    'label' => false,
                    'user' => $user,
                    'states' => $states,
                    'business' => $business,
                ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Package::class,
            'user' => null,
            'business' => null,
            'isApproved' => false,
            'industryType' => null,
            'states' => null,
            'quote'=> null,
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_package';
    }
}
