<?php

namespace LocalsBest\UserBundle\Command;

use DateTime;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\EmailTemplate;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\NonReceivedEmailScheduler;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NonReceivedListingEmailsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('non-received-listing-emails')
            ->setDescription('Command will take scheduler expire date and send mail to users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // Get NRL Emails Campaigns
        $q = $this->em ->createQuery(
            "select n from LocalsBest\UserBundle\Entity\NonReceivedEmailScheduler n where n.status = true"
        );
        $schedulers = $q->getResult();

        if (count($schedulers) > 0) {
            /** @var NonReceivedEmailScheduler $scheduler */
            foreach($schedulers as $scheduler) {
                if($scheduler->getMlsNumber() !== null && $scheduler->getMlsNumber() !== '') {
                    $mlsCheck = $this->em->getRepository('LocalsBestUserBundle:TransactionProperty')
                        ->findOneBy(['mlsNumber' => $scheduler->getMlsNumber()]);

                    if($mlsCheck === null && strpos(strtolower($scheduler->getMlsNumber()), 'p') === 0) {
                        $mlsCheck = $this->em->getRepository('LocalsBestUserBundle:TransactionProperty')
                            ->findOneBy(['p_mls_number' => $scheduler->getMlsNumber()]);
                    }

                    if($mlsCheck === null) {
                        $business = $scheduler->getBusiness();
                        $sDate = $scheduler->getCreatedAt();
                        $now = new DateTime('now');
                        $daysPass = $now->diff($sDate)->d;
                        if( in_array($daysPass, [7, 10, 17]) || ( ($daysPass > 17) && (($daysPass - 17) % 7 == 0) ) ) {
                            // Get email options base on email number
                            switch ($daysPass) {
                                case 7:
                                    /** @var EmailTemplate $emailTemplate */
                                    $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')
                                        ->findOneBy(
                                            [
                                                'business' => $business,
                                                'category' => 'Non Received Listings',
                                                'template_number' => 2
                                            ]
                                        )
                                    ;

                                    if($emailTemplate !== null) {
                                        $body = str_replace(
                                            [
                                                '*agent_name*',
                                                '*transaction_mls_number*',
                                                '*transaction_address*'
                                            ],
                                            [
                                                $scheduler->getAgentName(),
                                                $scheduler->getMlsNumber(),
                                                $scheduler->getAddress()
                                            ],
                                            $emailTemplate->getEmailBody());
                                        $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                            ->setFrom('notifications@easycloses.com')
                                            ->setTo($scheduler->getEmailAddress())
                                            ->setBody(
                                                $this->getContainer()
                                                    ->get('templating')
                                                    ->render(
                                                        '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                        ['body' => $body]
                                                    ),
                                                'text/html'
                                            );
                                    }else{
                                        $message = (new Swift_Message('OOPS! You Forgot to Upload Your Listing Paperwork to Our Agent Power Platform'))
                                            ->setFrom('notifications@easycloses.com')
                                            ->setTo($scheduler->getEmailAddress())
                                            ->setBody(
                                                $this->getContainer()
                                                    ->get('templating')
                                                    ->render(
                                                        '@LocalsBestUser/transaction/non-received-email-2.html.twig',
                                                        ['data' => $scheduler]
                                                    ),
                                                'text/html'
                                            );
                                    }
                                    break;
                                case 10:
                                    $emailTemplate = $this->em
                                        ->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
                                            [
                                                'business' => $business,
                                                'category' => 'Non Received Listings',
                                                'template_number' => 3
                                            ]
                                        );

                                    if($emailTemplate !== null) {
                                        $body = str_replace(
                                            [
                                                '*agent_name*',
                                                '*transaction_mls_number*',
                                                '*transaction_address*'
                                            ],
                                            [
                                                $scheduler->getAgentName(),
                                                $scheduler->getMlsNumber(),
                                                $scheduler->getAddress()
                                            ],
                                            $emailTemplate->getEmailBody());
                                        $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                            ->setFrom('notifications@easycloses.com')
                                            ->setTo($scheduler->getEmailAddress())
                                            ->setBody(
                                                $this->getContainer()
                                                    ->get('templating')
                                                    ->render(
                                                        '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                        ['body' => $body]
                                                    ),
                                                'text/html'
                                            );
                                    }else {
                                        $message = (new Swift_Message('Urgent: Listing Paperwork Needed to Avoid a Fine'))
                                            ->setFrom('notifications@easycloses.com')
                                            ->setTo($scheduler->getEmailAddress())
                                            ->setBody(
                                                $this->getContainer()
                                                    ->get('templating')
                                                    ->render(
                                                        '@LocalsBestUser/transaction/non-received-email-3.html.twig',
                                                        ['data' => $scheduler]
                                                    ),
                                                'text/html'
                                            );
                                    }
                                    break;
                                case 17:
                                    $emailTemplate = $this->em
                                        ->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
                                            [
                                                'business' => $business,
                                                'category' => 'Non Received Listings',
                                                'template_number' => 4
                                            ]
                                        );

                                    if($emailTemplate !== null) {
                                        $body = str_replace(
                                            [
                                                '*agent_name*',
                                                '*transaction_mls_number*',
                                                '*transaction_address*'
                                            ],
                                            [
                                                $scheduler->getAgentName(),
                                                $scheduler->getMlsNumber(),
                                                $scheduler->getAddress()
                                            ],
                                            $emailTemplate->getEmailBody());
                                        $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                            ->setFrom('notifications@easycloses.com')
                                            ->setTo($scheduler->getEmailAddress())
                                            ->setBody(
                                                $this->getContainer()
                                                    ->get('templating')
                                                    ->render(
                                                        '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                        ['body' => $body]
                                                    ),
                                                'text/html'
                                            );
                                    }else {
                                        $message = (new Swift_Message('Urgent: You Have Been Fined For Failing to Provide Listing Paperwork'))
                                            ->setFrom('notifications@easycloses.com')
                                            ->setTo($scheduler->getEmailAddress())
                                            ->setBody(
                                                $this->getContainer()
                                                    ->get('templating')
                                                    ->render(
                                                        '@LocalsBestUser/transaction/non-received-email-4.html.twig',
                                                        ['data' => $scheduler]
                                                    ),
                                                'text/html'
                                            );
                                    }
                                    break;
                            }

                            if ( ($daysPass > 17) && (($daysPass - 17) % 7 == 0) ) {

                                $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')
                                    ->findOneBy(
                                        [
                                            'business' => $business,
                                            'category' => 'Non Received Listings', 'template_number' => 4
                                        ]
                                    )
                                ;

                                if($emailTemplate !== null) {
                                    $body = str_replace(
                                        [
                                            '*agent_name*',
                                            '*transaction_mls_number*',
                                            '*transaction_address*'
                                        ],
                                        [
                                            $scheduler->getAgentName(),
                                            $scheduler->getMlsNumber(),
                                            $scheduler->getAddress()
                                        ],
                                        $emailTemplate->getEmailBody()
                                    );
                                    $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                        ->setFrom('notifications@easycloses.com')
                                        ->setTo($scheduler->getEmailAddress())
                                        ->setBody(
                                            $this->getContainer()
                                                ->get('templating')
                                                ->render(
                                                    '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                    ['body' => $body]
                                                ),
                                            'text/html'
                                        );
                                } else {
                                    $message = (new Swift_Message('Urgent: You Have Been Fined For Failing to Provide Listing Paperwork'))
                                        ->setFrom('notifications@easycloses.com')
                                        ->setTo($scheduler->getEmailAddress())
                                        ->setBody(
                                            $this->getContainer()
                                                ->get('templating')
                                                ->render(
                                                    '@LocalsBestUser/transaction/non-received-email-4.html.twig',
                                                    ['data' => $scheduler]
                                                ),
                                            'text/html'
                                        );
                                }
                            }

                            $this->getContainer()->get('mailer')->send($message);

                            /** @var User $user */
                            $user = $this->em
                                ->getRepository('LocalsBestUserBundle:User')
                                ->find($scheduler->getAgentId());

                            if ($scheduler->getAgentId() !== null && $scheduler->getAgentId() > 0) {

                                // Create note about sent email
                                $note = new Note();
                                $note->setStatus(
                                    $this->em
                                        ->getRepository('LocalsBestCommonBundle:Note')
                                        ->getDefaultStatus()
                                );

                                $note->setNote("NR Email: MLS# " . $scheduler->getMlsNumber() . ", " . $scheduler->getAddress())
                                    ->setPrivate(true)
                                    ->setObjectType('LocalsBestUserBundle:User')
                                    ->setObjectId($scheduler->getAgentId())
                                    ->setUser($user)
                                    ->setCreatedBy($scheduler->getBusiness()->getOwner());

                                $this->em->persist($note);
                                $this->em->flush();

                                // Share note with Staff
                                $staff = $this->em
                                    ->getRepository('LocalsBestUserBundle:User')
                                    ->findStaff(
                                        $scheduler->getBusiness()->getOwner(),
                                        $scheduler->getBusiness()
                                    );

                                if(count($staff)) {
                                    foreach ($staff as $member) {
                                        $share = new Share();
                                        $share->setUser($member);

                                        $token = base64_encode(time() . ':' . rand());

                                        $share->setToken($token);
                                        $share->setObjectType(ObjectTypeType::User);
                                        $share->setCreatedBy($scheduler->getBusiness()->getOwner());
                                        $share->setObjectId($scheduler->getAgentId());

                                        $member->addShare($share);
                                        $note->addShare($share);
                                    }
                                }
                            }
                            $scheduler->setCounter($scheduler->getCounter() + 1);

                            // Create Call Event for Janis on 17 day
                            if( $daysPass == 17 && in_array($user->getBusinesses()->first()->getId(), [50])) {
                                $event = new Event();
                                $event->setTitle($scheduler->getMlsNumber() . ' | ' . $scheduler->getAddress())
                                    ->setDescription($user->getFullName() . ' | ' . $user->getPrimaryPhone()->getNumber())
                                    ->setTime(new DateTime('now'))
                                    ->setEndTime(new DateTime('+4 hours'))
                                    ->setType('Call')
                                    ->setUser($user)
                                    ->setStatus(
                                        $this->em
                                            ->getRepository('LocalsBest\CommonBundle\Entity\Status')
                                            ->findOneByStatus('new')
                                    )
                                    ->setOwner($scheduler->getBusiness())
                                    ->setCreatedBy($this->em->getRepository('LocalsBestUserBundle:User')->find(1986));

                                $this->em->persist($event);
                                $this->em->flush();
                                $user->addEvent($event);
                                $this->em->flush();
                            }
                            $this->em->flush();
                        }
                    }
                }
            }
        }

        $output->writeln('Done');
    }
}
