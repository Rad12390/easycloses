<?php

namespace LocalsBest\ShopBundle\Command;

use LocalsBest\UserBundle\Entity\Plugin;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddPluginsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('add-plugins')
            ->setDescription('Command will add Plugins Entities')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $pluginsList = [
            'advanced contacts screen' => 'Advanced contacts screen',
            'auto fill' => 'Transactions Auto Fill',
            'blank documents' => 'Blank Documents',
            'cda payment system' => 'Transaction CDA Payment System.',
            'company directory' => 'Company Directory',
            'client portals' => 'Client Portals',
            'cd client invitation' => 'Company Directory Client Invitation',
            'concierge service' => 'Concierge Service',
            'contacts tab' => 'Contacts Tab',
            'customers' => 'Customers',
            'employee profiles' => 'Employee profiles',
            'events & alerts' => 'Events & Alerts',
            'events & alerts sharing' => 'Events & Alerts Sharing',
            'form builder' => 'Form Builder for Vendors',
            'featured business directory-free' => 'Featured Business Directory-Free',
            'featured business directory-paid' => 'Featured Business Directory-Paid',
            'global business member search' => 'Global Business Member Search',
            'jobs & quotes' => 'Jobs & Quotes',
            'lead tracker' => 'Lead Tracker',
            'links' => 'Links Tab and Custom Login',
            '15 day expired listings' => 'Transaction 15 Day Expired Listings',
            'transactions clients page' => 'Transactions Clients Page',
            'non received documents' => 'Transactions Non Received Documents',
            'non received listings' => 'Transactions Non Received Listings',
            'notification settings text' => 'Notification Settings Text',
            'social post (old)' => 'Social Post (Old)',
            'social posting automatic article' => 'Social Posting Automatic Article',
            'social posting manual' => 'Social Posting (Manual)',
            'social posting re listing manual' => 'Social Posting RE Listing Manual',
            'social posting re listing automatic' => 'Social Posting RE Listing Automatic',
            'store tab' => 'Store Tab',
            'team' => 'Teams plugin',
            'totals' => 'Transaction Totals',
            'transactions' => 'Transactions + Transaction Documents',
            'wp posting' => 'WP posting',
            'text info package' => 'Text Info Package',
        ];

        foreach ($pluginsList as $slug => $name) {
            $plugin = $this->em->getRepository('LocalsBestUserBundle:Plugin')->findOneBy(['slug' => $slug]);

            if ($plugin !== null) {
                continue;
            }

            $plugin = new Plugin();
            $plugin->setName($name);
            $plugin->setSlug($slug);

            $this->em->persist($plugin);
            $this->em->flush();

            $output->writeln('Plugin "' . $name . '" was added.');
        }
    }
}