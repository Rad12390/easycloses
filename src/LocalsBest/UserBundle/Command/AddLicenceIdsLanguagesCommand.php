<?php

namespace LocalsBest\UserBundle\Command;

use LocalsBest\UserBundle\Entity\AssociationRow;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class AddLicenceIdsLanguagesCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('add-licences-languages-cities-ids')
            ->setDescription('Command will add Languages, Cities, Licences IDs to NY users')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        include('web/users_languages_cities.php');
        $i = 0;
        $batchSize = 50;


        foreach ($member_categories as $item) {
            $this->em->getFilters()->enable('softdeleteable');
            $this->em->getFilters()->disable('softdeleteable');

            /** @var \LocalsBest\UserBundle\Entity\User $user */
            $user = $this->em->getRepository('LocalsBestUserBundle:User')
                ->findOneBy(['portal_user_id' => $item['portal_user_id']]);

            if($user === null) {
                $output->writeln($i . ' ERROR! There is no user with portal ID: ' . $item['portal_user_id']);
                continue;
            }

            $user->setStateLicenseId($item['msl_board_id']);

            $cities = explode(',', $item['cities']);
            $languages = explode(',', $item['languages']);

            if($item['languages'] !== null) {
                foreach ($languages as $lang) {
                    $langObject = $this->em->getRepository('LocalsBestUserBundle:Language')
                        ->findOneBy(['language' => $lang]);

                    if(false === $user->getLanguages()->contains($langObject)) {
                        $user->addLanguage($langObject);
                    }
                }
            }

            $this->em->flush();
            $output->writeln($i . ' COMPLETE! For users with portal ID: ' . $item['portal_user_id'] );

            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $this->em->flush();

        include('web/member_categories_cities.php');
        $i = 0;

        foreach ($member_categories as $item) {
            $this->em->getFilters()->enable('softdeleteable');
            $this->em->getFilters()->disable('softdeleteable');

            /** @var \LocalsBest\UserBundle\Entity\User $user */
            $user = $this->em->getRepository('LocalsBestUserBundle:User')
                ->findOneBy(['portal_user_id' => $item['portal_user_id']]);

            if($user === null) {
                $output->writeln($i . ' ERROR! There is no user with portal ID: ' . $item['portal_user_id']);
                continue;
            }

            $cities = explode(',', $item['cities']);

            if($item['cities'] !== null) {
                foreach ($cities as $city) {
                    $state = $this->em->getRepository('LocalsBestUserBundle:State')->find(36);
                    $cityObject = $this->em->getRepository('LocalsBestUserBundle:City')
                        ->findOneBy(['name' => $city, 'state' => $state]);

                    if(null !== $cityObject && false === $user->getWorkingCities()->contains($cityObject)) {
                        $user->addWorkingCities($cityObject);
                    }
                }
            }

            $this->em->flush();
            $output->writeln($i . ' COMPLETE! For users with portal ID: ' . $item['portal_user_id'] );

            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $this->em->flush();
    }
}