<?php

namespace LocalsBest\UserBundle\Command;

use LocalsBest\UserBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCitiesForNYCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('add-ny-cities')
            ->setDescription('Command will add Cities for for NY state')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        include('web/ny_cities.php');
        $i = 0;
        $batchSize = 100;

        foreach ($zipcodes as $item) {
            $state = $this->em->getRepository('LocalsBestUserBundle:State')->find(36);
            $cityExist = $this->em->getRepository('LocalsBestUserBundle:City')
                ->findOneBy(['name' => $item['city'], 'state' => $state]);

            if($cityExist !== null) {
                $output->writeln($i . ' ERROR! City with name already exist: ' . $item['city']);
                continue;
            }

            $newCity = new City();
            $newCity->setName(ucwords(strtolower($item['city'])));
            $newCity->setState($state);
            $this->em->persist($newCity);
            $this->em->flush();

            $output->writeln($i . ' COMPLETE! Add new city: ' . $item['city']);

            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $this->em->flush();
    }
}