<?php

namespace LocalsBest\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class ApproveDocsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('approve-documents')
            ->setDescription('Command will approve docs attached to Contract fell thru transactions by logs')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $closings = $this->em->getRepository('LocalsBestUserBundle:Closing')
            ->findBy(['status' => 'Contract_fell_thru']);

        if (count($closings) > 0) {
            foreach($closings as $closing) {

                $transaction = $closing->getTransaction();
                if($transaction === null) {
                    continue;
                }

                $logs = $this->em->getRepository('LocalsBestUserBundle:Log')->findBy(['transaction' => $transaction]);

                foreach($logs as $log) {
                    $logText = explode(':', $log->getLog());

                    if($logText[0] == 'Approved document') {
                        $documentTypeName  = trim($logText[1]);
                        /** @var \LocalsBest\UserBundle\Entity\DocumentType $documentType */
                        $documentType = $this->em->getRepository('LocalsBestUserBundle:DocumentType')
                            ->findOneBy(['closing' => $closing, 'name' => $documentTypeName, 'deleted' => null]);

                        if ($documentType != null) {
                            $documentType->setApproved(true)->setRejected(false)->setStatus('A');

                            $message = "APPROVED: document type: " . $documentType->getName() . " ID: #"
                                . $documentType->getId();
                            $output->writeln($message);
                        } else {
                            $message = "document type " . $documentTypeName . " doesn't exist for closing #"
                                . $closing->getId();
                            $output->writeln($message);
                        }
                    }
                    $this->em->flush();
                }
            }
        }
    }
}