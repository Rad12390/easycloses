<?php

namespace LocalsBest\UserBundle\Command;

use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Log;
use LocalsBest\UserBundle\Entity\Share;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class CompleteCallEventsForNonReceivedCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('complete-call-events-for-non-received')
            ->setDescription('Complete call events for non received listings')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $newStatus = $this->em->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('new');
        // Get Events
        $q = $this->em->createQuery("select e from LocalsBest\UserBundle\Entity\Event e where e.type = :type and e.createdBy = :user and e.time like :time and e.end_time like :time and e.status = :status")
            ->setParameter('type', 'Call')
            ->setParameter('user', $this->em->getRepository('LocalsBestUserBundle:User')->find(1986))
            ->setParameter('status', $newStatus)
            ->setParameter('time', date("Y-m-d") . "%");

        $events = $q->getResult();

        if (count($events) > 0) {
            /** @var \LocalsBest\UserBundle\Entity\NonReceivedEmailScheduler $event */
            foreach($events as $event) {
                $event->setStatus($this->em->getRepository('LocalsBest\CommonBundle\Entity\Status')
                    ->findOneByStatus('completed'));
                $this->em->flush();
            }
        }
        $output->writeln('Done');
    }
}