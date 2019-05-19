<?php

namespace LocalsBest\UserBundle\Command;

use DateInterval;
use DateTime;
use LocalsBest\UserBundle\Entity\EmailTemplate;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Log;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExpireListingCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('expire-listings')
            ->setDescription('Command will take all listings that have 2 week to expire date and send mail to '
                . 'users that create transaction for this listing.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // Get start of day 15 days before
        $dateStart = new DateTime();
        $dateStart->add(new DateInterval('P15D'));
        $dateStart = $dateStart->format('Y-m-d') . ' 00:00:00';
        // Get end of day 15 days before
        $dateEnd = new DateTime();
        $dateEnd->add(new DateInterval('P15D'));
        $dateEnd = $dateEnd->format('Y-m-d') . ' 23:59:59';
        // Get events for needed date
        $q = $this->em->createQuery("select e from LocalsBest\UserBundle\Entity\Event e where e.title = 'Expiration Date' and e.time between :dateStart and :dateEnd and e.deleted is null")
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd);

        $events = $q->getResult();

        $transStatuses = ['Withdrawn', 'Expired', 'Temporarily_Off_Market', 'Sold_Paid', 'Sold_not_Paid'];

        if (count($events) > 0) {
            /** @var Event $event */
            foreach($events as $event) {
                // Get events with out closing file
                if($event->getClosing() !== null) {
                    continue;
                }
                // only non deleted transactions
                if(
                    $event->getListing()->getTransaction() !== null
                    && $event->getListing()->getTransaction()->getDeleted() === null
                ) {
                    if(
                        $event->getListing()->getTransaction()->getClosing() !== null
                        && in_array($event->getListing()->getTransaction()->getTransactionStatus(), $transStatuses)
                    ) {
                        continue;
                    }

                    $transaction = $event->getListing()->getTransaction();
                } else {
                    continue;
                }

                if ($event->getCreatedBy() === null) {
                    $user = $event->getListing()->getTransaction()->getCreatedBy();
                } else {
                    $user = $event->getCreatedBy();
                }

                if (!$this->getContainer()->get('localsbest.checker')->forAddon('15 day expired listings', $user)) {
                    continue;
                }

                $business = $user->getBusinesses()[0];

                /** @var EmailTemplate $emailTemplate */
                $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')
                    ->findOneBy(['business' => $business, 'category' => 'Expire Listings', 'template_number' => 1]);

                if($emailTemplate !== null) {
                    // Get Transaction info to Email
                    $body = str_replace(
                        [
                            '*agent_first_name*',
                            '*agent_last_name*',
                            '*event_time*',
                        ],
                        [
                            $user->getFirstName(),
                            $user->getLastName(),
                            $event->getTime()->format("m/d/Y"),
                        ],
                        $emailTemplate->getEmailBody()
                    );
                    //Send Email to User
                    $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                        ->setFrom('notifications@easycloses.com')
                        ->setTo($user->getPrimaryEmail()->getEmail())
                        ->setBody($this->getContainer()->get('templating')
                            ->render(
                                '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                ['body' => $body]
                            ),
                            'text/html'
                        );

                    $this->getContainer()->get('mailer')->send($message);
                }else {
                    $this->getContainer()->get('localsbest.mailman')->listingExpiredMail($event, $user);
                }

                if($transaction !== null) {
                    // Create Log for Transaction about this
                    $log = new Log();
                    $logText = 'Application send email to ' . $user->getFirstName() . ' ' . $user->getLastName()
                        . ' about the Listing File will be expired in 15 days';
                    $log->setCreatedBy($business->getOwner())->setLog($logText);
                    $log->setTransaction($transaction);
                    $this->em->persist($log);

                    $transaction->addLog($log);
                    $this->em->flush();
                }
                // send message to console about script end
                $output->writeln(
                    'Application send Alert Email to user #' . $user->getId() . '(' . $user->getFirstName() . ' '
                    . $user->getLastName() . ') for Event #' . $event->getId() . '(' . $event->getTitle() . ') at '
                    . date('m-d-Y')
                );
            }
        }

        $output->writeln('Script done.');
    }
}
