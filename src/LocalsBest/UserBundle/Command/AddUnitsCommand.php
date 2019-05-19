<?php

namespace LocalsBest\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class AddUnitsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('add-units')
            ->setDescription('Command will add #Unit to transaction')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        include('web/transactions_units.php');
        $i = 0;
        $batchSize = 100;

        foreach ($transactions as $item) {
            $object = $this->em->getRepository('LocalsBestUserBundle:Closing')
                ->findOneBy(['portalTransactionId' => $item['transaction_id']]);

            if($object === null) {
                $object = $this->em->getRepository('LocalsBestUserBundle:Listing')
                    ->findOneBy(['portalTransactionId' => $item['transaction_id']]);
            }

            if($object === null) {
                $output->writeln($i . ' ERROR! There is no object with portal ID: ' . $item['transaction_id']);
                continue;
            }

            $transaction = $object->getTransaction();

            if($transaction === null) {
                $output->writeln($i . ' ERROR! There is no object with portal ID: ' . $item['transaction_id']);
                continue;
            }

            $tProperties = $transaction->getTransactionProperty();
            $tProperties->setMlsBoard($item['unit']);
            $output->writeln($i . ' COMPLETE! Add #Unit for object with portal ID: ' . $item['transaction_id']);

            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->em->flush();
    }
}