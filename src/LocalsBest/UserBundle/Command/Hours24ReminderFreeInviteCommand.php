<?php

namespace LocalsBest\UserBundle\Command;

use DateInterval;
use DateTime;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Hours24ReminderFreeInviteCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('reminder:24-hours-free-invitation')
            ->setDescription('Command will remind to user about invitation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // Get day of week
        $dayOfWeek = date("N");
        // if it's weekend
        if($dayOfWeek == 6 || $dayOfWeek == 7) {
            $output->writeln("Script doesn't work at weekend!");
            return false;
        }
        // Get start and end of prev day
        $interval = new DateInterval('P1D');

        $start = new DateTime();
        $start->sub($interval)->setTime(0,0,0);
        $output->writeln($start->getTimestamp());

        $end = new DateTime();
        $end->sub($interval)->setTime(23,59,59);
        $output->writeln($end->getTimestamp());
        // Get invites for needed day
        $invites = $this->em->getRepository('LocalsBestUserBundle:Invite')->findInvitesByInterval($start, $end);
        foreach ($invites as $invite) {
            // Send Email
            $message = (new Swift_Message('Get More Business with a FREE Easy Closes Listing'))
                ->setFrom('EasyCloses@mylocalsbest.com')
                ->setTo($invite->getEmail())
                ->setBody(
                    $this->getContainer()
                        ->get('templating')
                        ->render(
                            '@LocalsBestNotification/mails/invitation-reminder-24hours.html.twig',
                            ['invite' => $invite]
                        ),
                    'text/html'
                );

            $this->getContainer()->get('mailer')->send($message);
        }
        // Show message about end of script
        $output->writeln('script done.');
        return null;
    }
}