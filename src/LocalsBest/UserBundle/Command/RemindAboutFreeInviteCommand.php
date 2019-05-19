<?php

namespace LocalsBest\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemindAboutFreeInviteCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('reminder:free-invitation')
            ->setDescription('Command will remind to user about invitation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // Get day of week
        $dayOfWeek = date("N");
        // Stop on weekend
        if($dayOfWeek == 6 || $dayOfWeek == 7) {
            $output->writeln("Script doesn't work at weekend!");
            return false;
        }
        // if it's monday => get day 3 days before else one day before
        if($dayOfWeek == 1) {
            $interval = new \DateInterval('P3D');
        } else {
            $interval = new \DateInterval('P1D');
        }
        // get start and end of day
        $start = new \DateTime();
        $start->sub($interval)->setTime(0,0,0);
        $output->writeln($start->getTimestamp());

        $end = new \DateTime();
        $end->sub(new \DateInterval('P1D'))->setTime(23,59,59);
        $output->writeln($end->getTimestamp());
        // Get invites for needed day
        $invites = $this->em->getRepository('LocalsBestUserBundle:Invite')->findInvitesByInterval($start, $end);

        foreach ($invites as $invite) {
            // Send Email
            $message = (new \Swift_Message('Get More Business with a FREE Easy Closes Listing'))
                ->setFrom('EasyCloses@mylocalsbest.com')
                ->setTo($invite['email'])
                ->setBody(
                    $this->getContainer()
                        ->get('templating')
                        ->render(
                            '@LocalsBestNotification/mail/invitation-reminder.html.twig',
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