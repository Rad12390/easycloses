<?php

namespace LocalsBest\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\Product;
use LocalsBest\UserBundle\Entity\ProductCategory;
use LocalsBest\UserBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('title', Type\TextType::class, [
                'required' => false,
            ])
            ->add('short_description', Type\TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 6,
                ],
            ])
            ->add('description', Type\TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 6,
                ],
            ])
            ->add('isAbleForShop', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('linkTo', Type\TextType::class, [
                'required' => false,
            ])
            ->add('addon_part', Type\ChoiceType::class, [
                'placeholder' => '-Select App Part-',
                'required' => false,
                'empty_data' => null,
                'choices' => [
                    'Advanced contacts screen' => 'Advanced contacts screen',
                    'Transactions Auto Fill' => 'auto fill',
                    'Blank Documents' => 'blank documents',
                    'Transaction CDA Payment System.' => 'cda payment system',
                    'Company Directory' => 'company directory',
                    'Client Portals' => 'client portals',
                    'Company Directory Client Invitation' => 'cd client invitation',
                    'Concierge Service' => 'concierge service',
                    'Contacts Tab' => 'contacts tab',
                    'Customers' => 'customers',
                    'Employee profiles' => 'employee profiles',
                    'Events & Alerts' => 'events & alerts',
                    'Events & Alerts Sharing' => 'events & alerts sharing',
                    'Form Builder for Vendors' => 'form builder',
                    'Featured Business Directory-Free' => 'featured business directory-free',
                    'Featured Business Directory-Paid' => 'featured business directory-paid',
                    'Global Business Member Search' => 'global business member search',
                    'Jobs & Quotes' => 'jobs & quotes',
                    'Lead Tracker' => 'lead tracker',
                    'Links Tab and Custom Login' => 'links',
                    'Transaction 15 Day Expired Listings' => '15 day expired listings',
                    'Transactions Clients Page' => 'transactions clients page',
                    'Transactions Non Received Documents' => 'non received documents',
                    'Transactions Non Received Listings' => 'non received listings',
                    'Notification Settings Text' => 'notification settings text',
                    'Social Post (Old)' => 'social post (old)',
                    'Social Posting Automatic Article' => 'social posting automatic article',
                    'Social Posting (Manual)' => 'social posting manual',
                    'Social Posting RE Listing Manual' => 'social posting re listing manual',
                    'Social Posting RE Listing Automatic' => 'social posting re listing automatic',
                    'Store Tab' => 'store tab',
                    'Teams Addon' => 'team',
                    'Transaction Totals' => 'totals',
                    'Transactions + Transaction Documents' => 'transactions',
                    'WP posting' => 'wp posting',
                ]
            ])
            ->add('addon_type', Type\ChoiceType::class, [
                'placeholder' => '-Select Addon Type-',
                'required' => false,
                'empty_data' => null,
                'choices' => [
                    'For Individual' => 'single',
                    'For Managers & Assistants (Business)' => 'managers&assistants',
                    'For Managers to Agents (Business)' => 'managers-agents',
                    'For Managers to Clients (Business)' => 'managers-clients',
                    'For Clients' => 'clients',
                ]
            ])
            ->add('images', Type\CollectionType::class, [
                'label' => false,
                'entry_type' => ProductImageType::class,
                'allow_add'    => true,
                'by_reference' => false,
                'entry_options' => array('label' => false)
            ])
            ->add('categories', EntityType::class, [
                'class' => ProductCategory::class,
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p_c')
                        ->orderBy('p_c.title', 'ASC');
                },
                'multiple' => true,
                'expanded' => false,
                'empty_data'  => null,
                'required' => false,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            $product = $event->getData();
            $form = $event->getForm();

            // check if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$product || null === $product->getId()) {
                $form->add('parent', EntityType::class, [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('p')
                            ->where('p.created_by = :user')
                            ->setParameter('user', $user)
                            ->orderBy('p.title', 'ASC');
                    },
                    'empty_data' => null,
                    'placeholder' => '- No parent -',
                ]);
            } else {
                $form->add('parent', EntityType::class, [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($user, $product) {
                        return $er->createQueryBuilder('p')
                            ->where('p.created_by = :user')
                            ->andWhere('p.id <> :id')
                            ->setParameter('user', $user)
                            ->setParameter('id', $product->getId())
                            ->orderBy('p.title', 'ASC');
                    },
                    'empty_data' => null,
                    'placeholder' => '- No parent -',
                ]);
            }
        });

        $builder
            ->add('isAbleForSlider', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('forBusiness', EntityType::class, [
                'required' => false,
                'class' => Business::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('b')->orderBy('b.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forPage', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'Home' => 'home',
                    'Transaction' => 'transaction',
                ],
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forStatus', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'Job' => [
                        'New' => 'New',
                        'Open' => 'Open',
                        'Closed' => 'Closed',
                        'Updated' => 'Updated',
                    ],
                    'Transaction' => [
                        'Pending' => 'Pending',
                        'Active' => 'Active',
                        'Cancelled' => 'Cancelled',
                        'Expired' => 'Expired',
                        'Leased' => 'Leased',
                        'Withdrawn' => 'Withdrawn',
                        'Contract fell thru' => 'Contract_fell_thru',
                        'Leased not Paid' => 'Leased_not_Paid',
                        'Leased Paid' => 'Leased_Paid',
                        'Sold not Paid' => 'Sold_not_Paid',
                        'Sold Paid' => 'Sold_Paid',
                        'Rejected Offer' => 'Rejected_Offer',
                    ],
                ],
                'choices_as_values' => true,
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forRoles', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'Customer Service(CSR)' => 'ROLE_CUSTOMER_SERVIC',
                    'Network Manager' => 'ROLE_NETWORK_MANAGER',
                    'Manager' => 'ROLE_MANAGER',
                    'Assistant Manager' => 'ROLE_ASSIST_MANAGER',
                    'Team Leader' => 'ROLE_TEAM_LEADER',
                    'Agent' => 'transaction',
                ],
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forState', EntityType::class, [
                'required' => false,
                'class' => State::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('forIndustry', EntityType::class, [
                'required' => false,
                'class' => IndustryType::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('i')->orderBy('i.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'select2'
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
            'user' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_product';
    }
}
