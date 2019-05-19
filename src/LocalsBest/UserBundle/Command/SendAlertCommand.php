<?php

namespace LocalsBest\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class SendAlertCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('check-for:alerts')
            ->setDescription('Command will take all alerts for current minute and send mail or/and text '
                . 'notifications to users that create event for this alert.')
        ;
    }

    /**
     * Get address suffix for event
     *
     * @param \LocalsBest\UserBundle\Entity\Event $event
     *
     * @return string
     */
    protected function getSuffix($event)
    {
        if($event->getTransaction() !== null) {
            return ' for ' . $event->getTransaction()->getFullAddress();
        } elseif($event->getJob() !== null) {
            return ' for ' . $event->getJob()->getFullAddress();
        } elseif($event->getClosing() !== null) {
            return ' for ' . $event->getClosing()->getTransaction()->getFullAddress();
        } elseif($event->getListing() !== null) {
            return ' for ' . $event->getListing()->getTransaction()->getFullAddress();
        } else {
            return '';
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // Get Current date and time
        $date = new DateTime();
        $date->setTime(date('H'), date('i'));
        // Get Alerts fot this date
        $alerts = $this->em->getRepository('LocalsBestUserBundle:EventAlert')->findBy(['date' => $date]);

        if (count($alerts) > 0) {
            foreach($alerts as $alert) {
                /** @var \LocalsBest\UserBundle\Entity\EventAlert $alert */
                $event = $alert->getEvent();
                $suffix = $this->getSuffix($event);
                // Create message text
                $message = 'Hi %firstname% %lastname% you have a %type% alert for %event% due %time%' . $suffix . '.';
                // Get Event owner
                $eventOwner = $event->getCreatedBy();

                if ($event->getAssignedTo()->count() > 0) {
                    $user = $event->getAssignedTo()->first();
                } else {
                    $user = $eventOwner;
                }
                // Set Event information to message
                $message = str_replace(
                    ['%firstname%', '%lastname%', '%type%', '%event%', '%time%'],
                    [
                        $user->getFirstName(),
                        $user->getLastName(),
                        $event->getType(),
                        $event->getTitle(),
                        $event->getTime()->format('m-d-Y H:i')
                    ],
                    $message
                );

                if($alert->getEmail() == true){
                    // Send Email
                    $this->getContainer()->get('localsbest.mailman')->eventAlertMail($event, $user, $message);
                    // Send message to console about Email
                    $output->writeln(
                        'Application send Alert Email to user #' . $user->getId() . '(' . $user->getFirstName()
                        . ' ' . $user->getLastName() . ') for Event #' . $event->getId() . '(' . $event->getTitle()
                        . ') at ' . date('m-d-Y H:i')
                    );
                }

                if($alert->getPhone() == true){
                    $phone = $user->getPrimaryPhone()->getNumber();
                    // Send SMS
                    sleep(1);
                    $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                    $sender = $this->getContainer()->get('jhg_nexmo_sms');
                    $sender->sendText('+1' . $number, $message, null);
                    // Send message to console about SMS
                    $output->writeln($message);
                    $output->writeln(
                        'Application send Alert Text to user #' . $user->getId() . '(' . $user->getFirstName() . ' '
                        . $user->getLastName() . ') for Event #' . $event->getId() . '(' . $event->getTitle()
                        . ') at ' . date('m-d-Y H:i')
                    );
                }
            }
        }
    }
}