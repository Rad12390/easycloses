<?php

namespace LocalsBest\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use LocalsBest\UserBundle\Services\DocsForApprove;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(RequestStack $requestStack, Container $container)
    {
        /** @var \LocalsBest\UserBundle\Entity\User $user */
        $user = $container->get('security.token_storage')->getToken()->getUser();
        $checker = $container->get('localsbest.checker');

        $business = $user->getBusinesses()->first();

        $isAdmin = $user->getRole()->getLevel() == 1;

        $menu = $this->factory->createItem('root');

        // Add "Home" Tab
        $menu
            ->addChild('Home', array('route' => 'locals_best_user_homepage',))
            ->setAttribute('icon', 'fa fa-home');

        // Add "Store" Tab
        if($checker->checkShopAccess()){
            $menu
                ->addChild('Shop', array('route' => 'ec_shop_main_page',))
                ->setAttribute('icon', 'fa fa-circle');
        }
        
        if (in_array($user->getRole()->getLevel(), [4]) && $checker->forAddon('packages', $user)) {
            $counter_quote = $container->get('localsbest.custom_quote')->getCount();
            
            
//        $menu
//                ->addChild('Shop Management', array('route' => 'sku_business_able',))
//                ->setAttribute('icon', 'fa fa-globe');
        
        $menu
            ->addChild('Sell in Shop', array(
                'uri' => 'javascript:;',
                'extras' => [
                    'routes' => [
                        ['route' => 'items'],
                        ['route' => 'packages'],
                        ['route' => 'combo'],
                        ['route' => 'tags'],
                        ['route' => 'categories'],
                        ['route' => 'items_show'],
                        ['route' => 'items_new'],
                        ['route' => 'items_create'],
                        ['route' => 'items_edit'],
                        ['route' => 'items_update'],
                        ['route' => 'items_delete'],
                        ['route' => 'packages_show'],
                        ['route' => 'packages_new'],
                        ['route' => 'packages_create'],
                        ['route' => 'packages_edit'],
                        ['route' => 'packages_update'],
                        ['route' => 'packages_delete'],
                        ['route' => 'combo_show'],
                        ['route' => 'combo_new'],
                        ['route' => 'combo_create'],
                        ['route' => 'combo_edit'],
                        ['route' => 'combo_update'],
                        ['route' => 'combo_delete'],
                        ['route' => 'tags_show'],
                        ['route' => 'tags_new'],
                        ['route' => 'tags_create'],
                        ['route' => 'tags_edit'],
                        ['route' => 'tags_update'],
                        ['route' => 'tags_delete'],
                        ['route' => 'categories_show'],
                        ['route' => 'categories_new'],
                        ['route' => 'categories_create'],
                        ['route' => 'categories_edit'],
                        ['route' => 'categories_update'],
                        ['route' => 'categories_delete'],

                        ['route' => 'items_for_approve_list'],
                        ['route' => 'sku_for_approve_list'],
                        ['route' => 'sku_business_able'],

                        ['route' => 'order_vendor_list'],

                        ['route' => 'shop_admin_list'],
                    ],
                ],
            ))
            ->setAttribute('icon', 'fa fa-shopping-cart')
            ->setAttribute('class', 'nav-item')
            ->setAttribute('subMenu', true);
        
        $menu['Sell in Shop']
            ->addChild('Items', array(
                'route' => 'items',
                'extras' => [
                    'routes' => [
                        ['route' => 'items_show'],
                        ['route' => 'items_new'],
                        ['route' => 'items_create'],
                        ['route' => 'items_edit'],
                        ['route' => 'items_update'],
                        ['route' => 'items_delete'],
                    ],
                ],
            ))
            ->setAttribute('icon', 'fa fa-puzzle-piece');

//        if (in_array($user->getRole()->getLevel(), [1, 4])) {
//            $menu['Sell in Shop']
//                ->addChild('Approve Items', array(
//                    'route' => 'items_for_approve_list',
//                ))
//                ->setAttribute('icon', 'fa fa-globe');
//        }

        $menu['Sell in Shop']
            ->addChild('Packages', array(
                'route' => 'packages',
                'extras' => [
                    'routes' => [
                        ['route' => 'packages_show'],
                        ['route' => 'packages_new'],
                        ['route' => 'packages_create'],
                        ['route' => 'packages_edit'],
                        ['route' => 'packages_update'],
                        ['route' => 'packages_delete'],
                    ],
                ],

            ))
            ->setAttribute('icon', 'fa fa-plane');

        $menu['Sell in Shop']
            ->addChild('Combos', array(
                'route' => 'combo',
                'extras' => [
                    'routes' => [
                        ['route' => 'combo_show'],
                        ['route' => 'combo_new'],
                        ['route' => 'combo_create'],
                        ['route' => 'combo_edit'],
                        ['route' => 'combo_update'],
                        ['route' => 'combo_delete'],
                    ],
                ],

            ))
            ->setAttribute('icon', 'fa fa-recycle');

        if (in_array($user->getRole()->getLevel(), [1])) {
            $menu['Sell in Shop']
                ->addChild('Approve SKUs', array(
                    'route' => 'sku_for_approve_list',
                ))
                ->setAttribute('icon', 'fa fa-globe');

            $menu['Sell in Shop']
                ->addChild('Shop Orders', array(
                    'route' => 'shop_admin_list',
                ))
                ->setAttribute('icon', 'fa fa-check');
	    
	    $menu['Sell in Shop']
                ->addChild('Payouts', array(
                    'route' => 'shop_payouts',
                ))
                ->setAttribute('icon', 'fa fa-money');
        }

        $menu['Sell in Shop']
            ->addChild('Vendor Orders', array(
                'route' => 'order_vendor_list',
            ))
            ->setAttribute('icon', 'fa fa-globe');
        
//        $menu['Sell in Shop']
//            ->addChild('Payments Results', array(
//                'route' => 'shop_manager_list',
//            ))
//            ->setAttribute('icon', 'fa fa-check');
        
        $menu['Sell in Shop']
            ->addChild('Custom Quotes', array(
                'route' => 'listing_custom_quotes',
            ))
            ->setAttribute('icon', 'fa fa-quote-left')
            ->setAttribute('badget', $counter_quote);
        
        $counter_request = $container->get('localsbest.contact_request')->getUnreadCount();
        
        $menu['Sell in Shop']
                ->addChild('Contact Us Requests', array('route' => 'vendor_contact_request',))
                ->setAttribute('icon', 'fa fa-comment')
                ->setAttribute('badget', $counter_request);

        if (in_array($user->getRole()->getLevel(), [1])) {
                $menu['Sell in Shop']
                    ->addChild('Tags', array(
                        'route' => 'tags',
                        'extras' => [
                            'routes' => [
                                ['route' => 'tags_show'],
                                ['route' => 'tags_new'],
                                ['route' => 'tags_create'],
                                ['route' => 'tags_edit'],
                                ['route' => 'tags_update'],
                                ['route' => 'tags_delete'],
                            ],
                        ],

                    ))
                    ->setAttribute('icon', 'fa fa-tags');

                $menu['Sell in Shop']
                    ->addChild('Categories', array(
                        'route' => 'categories',
                        'extras' => [
                            'routes' => [
                                ['route' => 'categories_show'],
                                ['route' => 'categories_new'],
                                ['route' => 'categories_create'],
                                ['route' => 'categories_edit'],
                                ['route' => 'categories_update'],
                                ['route' => 'categories_delete'],
                            ],
                        ],

                    ))
                    ->setAttribute('icon', 'fa fa-database');
            }
        }

        if (in_array($user->getRole()->getLevel(), [1,5]) && $checker->forAddon('packages', $user)) {
        $menu
            ->addChild('Sell in Shop', array(
                'uri' => 'javascript:;',
                'extras' => [
                    'routes' => [
                        ['route' => 'items'],
                        ['route' => 'packages'],
                        ['route' => 'combo'],
                        ['route' => 'tags'],
                        ['route' => 'categories'],
                        ['route' => 'items_show'],
                        ['route' => 'items_new'],
                        ['route' => 'items_create'],
                        ['route' => 'items_edit'],
                        ['route' => 'items_update'],
                        ['route' => 'items_delete'],
                        ['route' => 'packages_show'],
                        ['route' => 'packages_new'],
                        ['route' => 'packages_create'],
                        ['route' => 'packages_edit'],
                        ['route' => 'packages_update'],
                        ['route' => 'packages_delete'],
                        ['route' => 'combo_show'],
                        ['route' => 'combo_new'],
                        ['route' => 'combo_create'],
                        ['route' => 'combo_edit'],
                        ['route' => 'combo_update'],
                        ['route' => 'combo_delete'],
                        ['route' => 'tags_show'],
                        ['route' => 'tags_new'],
                        ['route' => 'tags_create'],
                        ['route' => 'tags_edit'],
                        ['route' => 'tags_update'],
                        ['route' => 'tags_delete'],
                        ['route' => 'categories_show'],
                        ['route' => 'categories_new'],
                        ['route' => 'categories_create'],
                        ['route' => 'categories_edit'],
                        ['route' => 'categories_update'],
                        ['route' => 'categories_delete'],

                        ['route' => 'items_for_approve_list'],
                        ['route' => 'sku_for_approve_list'],
                        ['route' => 'sku_business_able'],

                        ['route' => 'order_vendor_list'],

                        ['route' => 'shop_admin_list'],
                    ],
                ],
            ))
            ->setAttribute('icon', 'fa fa-shopping-cart')
            ->setAttribute('class', 'nav-item')
            ->setAttribute('subMenu', true);
        
        $menu['Sell in Shop']
            ->addChild('Items', array(
                'route' => 'items',
                'extras' => [
                    'routes' => [
                        ['route' => 'items_show'],
                        ['route' => 'items_new'],
                        ['route' => 'items_create'],
                        ['route' => 'items_edit'],
                        ['route' => 'items_update'],
                        ['route' => 'items_delete'],
                    ],
                ],
            ))
            ->setAttribute('icon', 'fa fa-puzzle-piece');

//        if (in_array($user->getRole()->getLevel(), [1, 4])) {
//            $menu['Sell in Shop']
//                ->addChild('Approve Items', array(
//                    'route' => 'items_for_approve_list',
//                ))
//                ->setAttribute('icon', 'fa fa-globe');
//        }

        $menu['Sell in Shop']
            ->addChild('Packages', array(
                'route' => 'packages',
                'extras' => [
                    'routes' => [
                        ['route' => 'packages_show'],
                        ['route' => 'packages_new'],
                        ['route' => 'packages_create'],
                        ['route' => 'packages_edit'],
                        ['route' => 'packages_update'],
                        ['route' => 'packages_delete'],
                    ],
                ],

            ))
            ->setAttribute('icon', 'fa fa-plane');

        $menu['Sell in Shop']
            ->addChild('Combos', array(
                'route' => 'combo',
                'extras' => [
                    'routes' => [
                        ['route' => 'combo_show'],
                        ['route' => 'combo_new'],
                        ['route' => 'combo_create'],
                        ['route' => 'combo_edit'],
                        ['route' => 'combo_update'],
                        ['route' => 'combo_delete'],
                    ],
                ],

            ))
            ->setAttribute('icon', 'fa fa-recycle');

        if (in_array($user->getRole()->getLevel(), [1])) {
            $menu['Sell in Shop']
                ->addChild('Approve SKUs', array(
                    'route' => 'sku_for_approve_list',
                ))
                ->setAttribute('icon', 'fa fa-globe');

            $menu['Sell in Shop']
                ->addChild('Shop Orders', array(
                    'route' => 'shop_admin_list',
                ))
                ->setAttribute('icon', 'fa fa-check');
	    
	    $menu['Sell in Shop']
                ->addChild('Payouts', array(
                    'route' => 'shop_payouts',
                ))
                ->setAttribute('icon', 'fa fa-money');
        }

        $menu['Sell in Shop']
            ->addChild('Vendor Orders', array(
                'route' => 'order_vendor_list',
            ))
            ->setAttribute('icon', 'fa fa-globe');

        if (in_array($user->getRole()->getLevel(), [1])) {
                $menu['Sell in Shop']
                    ->addChild('Tags', array(
                        'route' => 'tags',
                        'extras' => [
                            'routes' => [
                                ['route' => 'tags_show'],
                                ['route' => 'tags_new'],
                                ['route' => 'tags_create'],
                                ['route' => 'tags_edit'],
                                ['route' => 'tags_update'],
                                ['route' => 'tags_delete'],
                            ],
                        ],

                    ))
                    ->setAttribute('icon', 'fa fa-tags');

                $menu['Sell in Shop']
                    ->addChild('Categories', array(
                        'route' => 'categories',
                        'extras' => [
                            'routes' => [
                                ['route' => 'categories_show'],
                                ['route' => 'categories_new'],
                                ['route' => 'categories_create'],
                                ['route' => 'categories_edit'],
                                ['route' => 'categories_update'],
                                ['route' => 'categories_delete'],
                            ],
                        ],

                    ))
                    ->setAttribute('icon', 'fa fa-database');
            }
        }
        
        
        // Add "Contacts" Tab
        if ($checker->forAddon('contacts tab', $user)) {
            $menu
                ->addChild('Contacts', array(
                    'route' => 'contact_index',
                    'extras' => [
                        'routes' => [
                            ['route' => 'contact_view'],
                            ['route' => 'contact_edit'],
                            ['route' => 'contact_add'],
                        ],
                    ],
                ))
                ->setAttribute('icon', 'fa fa-book')
            ;
        }
        
        // Add "Team Info" Tab
        if ($business->getId() == 54 && $user->getRole()->getLevel() == 6) {
            $menu
                ->addChild('Team Info', array('route' => 'team_list',))
                ->setAttribute('icon', 'fa fa-trademark');
        }

        // Add "Company Directory" Tab
        if (
            in_array($business->getId(), [179])
            || $checker->forAddon('transactions', $user)
            || $checker->forAddon('company directory', $user)
        ) {
            $menu
                ->addChild('Company Directory', array(
                    'route' => 'users_index',
                    'extras' => [
                        'routes' => [
                            ['route' => 'user_view'],
                            ['route' => 'users_invite'],
                            ['route' => 'users_profile_edit'],
                            ['route' => 'users_add'],
                        ],
                    ],
                ))
                ->setAttribute('icon', 'fa fa-users');
        }

        

        // Add "Documents" Tab for shredding
//        if (
//            in_array($business->getId(), [179])
//            && in_array($user->getRole()->getLevel(), [4])
//        ) {
//            $menu
//                ->addChild('Documents', array(
//                    'route' => 'shredding_documents',
//                    'extras' => [
//                        'routes' => [
//                            ['route' => 'document_typeview'],
//                        ],
//                    ],
//                ))
//                ->setAttribute('icon', 'fa fa-file-text-o')
//            ;
//        }

        // Add "Blank Documents" Tab
        if ($user->getRole()->getLevel() == 7 && $business->getId() == 50) {

        } else {
            if ($checker->forAddon('blank documents', $user)) {
                $menu
                    ->addChild('Blank Documents', array(
                        'route' => 'blank_docs_index',
                        'extras' => [
                            'routes' => [
                                ['route' => 'blank_docs_show'],
                                ['route' => 'blank_docs_add'],
                            ],
                        ],
                    ))
                    ->setAttribute('icon', 'fa fa-file-o');
            }
        }

        // Add "Events" Tab
        if ($checker->forAddon('events & alerts', $user)) {
            $menu
                ->addChild('Events & Alerts', array(
                    'route' => 'event_index',
                    'extras' => [
                        'routes' => [
                            ['route' => 'event_view'],
                            ['route' => 'event_edit'],
                            ['route' => 'event_add'],
                        ],
                    ],
                ))
                ->setAttribute('icon', 'fa fa-calendar');
        }

        // Add "Service Directory" Tab
        if (
            $checker->forAddon('featured business directory-free', $user)
            || $checker->forAddon('featured business directory-paid', $user)
        ) {
            $menu
                ->addChild('Service Directory', array(
                    'route' => 'service_index',
                    'extras' => [
                        'routes' => [
                            ['route' => 'invite_paid_service'],
                            ['route' => 'vendor_add'],
                        ],
                    ],
                ))
                ->setAttribute('icon', 'fa fa-database');
        }

        // Add "Admin" Tab with all functionality
        if ($user->getRole()->getLevel() == 1) {
            
            $menu
                ->addChild('Admin', array(
                    'uri' => 'javascript:;',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_businesses'],
                            ['route' => 'admin_businesses_new'],
                            ['route' => 'admin_businesses_edit'],
                            ['route' => 'admin_businesses_show'],
                            ['route' => 'admin_businesses_change_plan'],
                            ['route' => 'admin_customize_industry_groups'],
                            ['route' => 'admin_plans'],
                            ['route' => 'admin_plans_show'],
                            ['route' => 'admin_plans_new'],
                            ['route' => 'admin_plans_edit'],
                            ['route' => 'admin_industry_type'],
                            ['route' => 'admin_industry_type_show'],
                            ['route' => 'admin_industry_type_new'],
                            ['route' => 'admin_industry_type_edit'],
                            ['route' => 'admin_products'],
                            ['route' => 'admin_products_show'],
                            ['route' => 'admin_products_new'],
                            ['route' => 'admin_products_edit'],
                            ['route' => 'admin_products_modules'],
                            ['route' => 'admin_products_modules_new'],
                            ['route' => 'admin_products_modules_edit'],
                            ['route' => 'admin_products_modules_show'],
                            ['route' => 'admin_products_modules_attache'],
                            ['route' => 'admin_products_assign_to'],
                            ['route' => 'admin_product_type_new'],
                            ['route' => 'admin_product_type_edit'],
                            ['route' => 'admin_products-categories'],
                            ['route' => 'admin_products-categories_new'],
                            ['route' => 'admin_products-categories_edit'],
                            ['route' => 'admin_products-categories_show'],
                            ['route' => 'admin_system_message'],
                            ['route' => 'admin_system_message_new'],
                            ['route' => 'admin_system_message_edit'],
                            ['route' => 'admin_system_message_show'],
                            ['route' => 'admin_advertisement'],
                            ['route' => 'admin_advertisement_new'],
                            ['route' => 'admin_advertisement_edit'],
                            ['route' => 'admin_advertisement_show'],
                            ['route' => 'admin_tooltips'],
                            ['route' => 'admin_tooltips_new'],
                            ['route' => 'admin_tooltips_edit'],
                            ['route' => 'admin_tooltips_show'],
                            ['route' => 'admin_contact_us'],
                        ],
                    ],
                ))
                ->setAttribute('icon', 'fa fa-bolt')
                ->setAttribute('class', 'nav-item')
                ->setAttribute('subMenu', true)
            ;

            $menu['Admin']
                ->addChild('Businesses', array(
                    'route' => 'admin_businesses',
                    'extras' => [
                        'routes' => [
                            ['route' => 'transaction_add_listing'],
                            ['route' => 'admin_businesses_new'],
                            ['route' => 'admin_businesses_edit'],
                            ['route' => 'admin_businesses_show'],
                            ['route' => 'admin_businesses_change_plan'],
                            ['route' => 'admin_customize_industry_groups'],
                        ],
                    ],
                ))
                ->setAttribute('icon', 'fa fa-bank')
            ;
            $menu['Admin']
                ->addChild('Plans', array(
                    'route' => 'admin_plans',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_plans_show'],
                            ['route' => 'admin_plans_new'],
                            ['route' => 'admin_plans_edit'],
                        ],
                    ],

                ))
                ->setAttribute('icon', 'fa fa-plane')
            ;
            $menu['Admin']
                ->addChild('Industry Types', array(
                    'route' => 'admin_industry_type',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_industry_type_show'],
                            ['route' => 'admin_industry_type_new'],
                            ['route' => 'admin_industry_type_edit'],
                        ],
                    ],

                ))
                ->setAttribute('icon', 'fa fa-shield')
            ;
            $menu['Admin']
                ->addChild('Products', array(
                    'route' => 'admin_products',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_products_show'],
                            ['route' => 'admin_products_new'],
                            ['route' => 'admin_products_edit'],
                            ['route' => 'admin_products_assign_to'],
                            ['route' => 'admin_product_type_new'],
                            ['route' => 'admin_product_type_edit'],
                        ],
                    ],

                ))
                ->setAttribute('icon', 'fa fa-shopping-cart')
            ;
            $menu['Admin']
                ->addChild('Products', array(
                    'route' => 'admin_products',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_products_show'],
                            ['route' => 'admin_products_new'],
                            ['route' => 'admin_products_edit'],
                            ['route' => 'admin_products_assign_to'],
                            ['route' => 'admin_product_type_new'],
                            ['route' => 'admin_product_type_edit'],
                        ],
                    ],

                ))
                ->setAttribute('icon', 'fa fa-shopping-cart')
            ;
            
//            $menu['Admin']
//                ->addChild('Advertisement', array(
//                    'route' => 'admin_advertisement',
//                    'extras' => [
//                        'routes' => [
//                            ['route' => 'admin_advertisement_new'],
//                            ['route' => 'admin_advertisement_edit'],
//                            ['route' => 'admin_advertisement_show'],
//                        ],
//                    ],
//
//                ))
//                ->setAttribute('icon', 'fa fa-file-image-o')
//            ;
            $menu['Admin']
                ->addChild('Tooltips', array(
                    'route' => 'admin_tooltips',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_tooltips_new'],
                            ['route' => 'admin_tooltips_edit'],
                            ['route' => 'admin_tooltips_show'],
                        ],
                    ],

                ))
                ->setAttribute('icon', 'fa fa-info')
            ;
             $menu['Admin']
                ->addChild('Buttons', array(
                    'route' => 'admin_buttons',
                    'extras' => [
                        'routes' => [
                            ['route' => 'admin_buttons_new'],
                            ['route' => 'admin_buttons_edit'],
                            ['route' => 'admin_buttons_show'],
                        ],
                    ],

                ))
                ->setAttribute('icon', 'fa fa-info')
            ;
            $menu['Admin']
                ->addChild('Contact Us Requests', array(
                    'route' => 'admin_contact_us',
                ))
                ->setAttribute('icon', 'fa fa-comment')
            ;
//            $menu['Admin']
//                ->addChild('Analytics', array(
//                    'uri' => 'https://analytics.google.com/analytics/web/',
//                    'extras' => [
//                        'routes' => [
//                            ['route' => 'admin_advertisement_new'],
//                            ['route' => 'admin_advertisement_edit'],
//                            ['route' => 'admin_advertisement_show'],
//                        ],
//                    ],
//
//                ))
//                ->setAttribute('icon', 'fa fa-dashboard')
//                ->setLinkAttributes(array('target' => '_blank'))
//            ;
        }

        if ($checker->forAddon('concierge service', $user)) {
            $menu
                ->addChild('Concierge', array())
                ->setAttribute('concierge', true)
            ;
        }

        return $menu;
    }
}