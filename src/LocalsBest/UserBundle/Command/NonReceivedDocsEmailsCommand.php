<?php

namespace LocalsBest\UserBundle\Command;

use DateTime;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\DocumentType;
use LocalsBest\UserBundle\Entity\NonReceivedDocsEmailScheduler;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NonReceivedDocsEmailsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('non-received-docs-emails')
            ->setDescription('Command will take docs scheduler and send mail to users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // Get NRD Email Campaigns
        $q = $this->em->createQuery(
            "select n from LocalsBest\UserBundle\Entity\NonReceivedDocsEmailScheduler n where n.status = true"
        );

        $schedulers = $q->getResult();
        // Disable Soft Delete Filter
        $this->em->getFilters()->disable('softdeleteable');

        if (count($schedulers) > 0) {
            /** @var NonReceivedDocsEmailScheduler $scheduler */
            foreach($schedulers as $scheduler) {

                if($scheduler->getTransaction()->getDeleted() !== null) {
                    continue;
                }
                // Get Doc Types
                if($scheduler->getType() == 'listing') {
                    $docTypes = $scheduler->getTransaction()->getListing()->getDocumentTypes();
                } else {
                    $docTypes = $scheduler->getTransaction()->getClosing()->getDocumentTypes();
                }

                $docCounter = 0;
                /** @var DocumentType $docType */
                foreach ($docTypes as $docType) {
                    if(
                        $docType->getIsRequired() == true
                        && $docType->getDocument() === null
                        && $docType->getDeleted() === null
                    ) {
                        $docCounter++;
                    }
                }

                if($docCounter == 0) {
                    $scheduler->setStatus(false);
                    $this->em->flush();

                    continue;
                }

                $business = $scheduler->getTransaction()->getCreatedBy()->getBusinesses()->first();
                $sDate = $scheduler->getCreatedAt();
                $now = new DateTime('now');
                $daysPass = $now->diff($sDate)->d;
                // Select Email Template
                if( in_array($daysPass, [7, 10, 17]) || ( ($daysPass > 17) && (($daysPass - 17) % 7 == 0) ) ) {

                    switch ($daysPass) {
                        case 7:
                            $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
                                [
                                    'business' => $business,
                                    'category' => 'Non Received Documents',
                                    'template_number' => 2
                                ]
                            );

                            if($emailTemplate !== null) {
                                $body = str_replace(
                                    [
                                        '*agent_full_name*',
                                        '*transaction_address*',
                                        '*documents_count*',
                                    ],
                                    [
                                        $scheduler->getTransaction()->getCreatedBy()->getFullName(),
                                        $scheduler->getTransaction()->getFullAddress(),
                                        $docCounter,
                                    ],
                                    $emailTemplate->getEmailBody()
                                );

                                $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                    ->setFrom('notifications@easycloses.com')
                                    ->setTo($scheduler->getEmailAddress())
                                    ->setBody(
                                        $this->getContainer()->get('templating')
                                            ->render(
                                                '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                ['body' => $body]
                                            ),
                                        'text/html'
                                    );
                            }else {
                                $message = (new Swift_Message('EasyCloses.com: Transaction documents are missed.'))
                                    ->setFrom('notifications@easycloses.com')
                                    ->setTo($scheduler->getEmailAddress())
                                    ->setBody($this->getContainer()->get('templating')->render(
                                        '@LocalsBestUser/transaction/non-received-docs-email-templates/2.html.twig',
                                        [
                                            'user' => $scheduler->getTransaction()->getCreatedBy(),
                                            'transaction' => $scheduler->getTransaction(),
                                            'docs' => $docCounter,
                                        ]
                                    ), 'text/html');
                            }
                            break;
                        case 10:
                            $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
                                [
                                    'business' => $business,
                                    'category' => 'Non Received Documents',
                                    'template_number' => 3
                                ]
                            );

                            if($emailTemplate !== null) {
                                $body = str_replace(
                                    [
                                        '*agent_full_name*',
                                        '*transaction_address*',
                                        '*documents_count*',
                                    ],
                                    [
                                        $scheduler->getTransaction()->getCreatedBy()->getFullName(),
                                        $scheduler->getTransaction()->getFullAddress(),
                                        $docCounter,
                                    ],
                                    $emailTemplate->getEmailBody()
                                );

                                $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                    ->setFrom('notifications@easycloses.com')
                                    ->setTo($scheduler->getEmailAddress())
                                    ->setBody(
                                        $this->getContainer()->get('templating')
                                            ->render(
                                                '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                ['body' => $body]
                                            ),
                                        'text/html'
                                    );
                            }else {
                                $message = (new Swift_Message('EasyCloses.com: Transaction documents are missed.'))
                                    ->setFrom('notifications@easycloses.com')
                                    ->setTo($scheduler->getEmailAddress())
                                    ->setBody($this->getContainer()->get('templating')->render(
                                        '@LocalsBestUser/transaction/non-received-docs-email-templates/3.html.twig',
                                        [
                                            'user' => $scheduler->getTransaction()->getCreatedBy(),
                                            'transaction' => $scheduler->getTransaction(),
                                            'docs' => $docCounter,
                                        ]
                                    ), 'text/html');
                            }
                            break;
                        case 17:
                            $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
                                [
                                    'business' => $business,
                                    'category' => 'Non Received Documents',
                                    'template_number' => 4
                                ]
                            );

                            if($emailTemplate !== null) {
                                $body = str_replace(
                                    [
                                        '*agent_full_name*',
                                        '*transaction_address*',
                                        '*documents_count*',
                                    ],
                                    [
                                        $scheduler->getTransaction()->getCreatedBy()->getFullName(),
                                        $scheduler->getTransaction()->getFullAddress(),
                                        $docCounter,
                                    ],
                                    $emailTemplate->getEmailBody()
                                );

                                $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                    ->setFrom('notifications@easycloses.com')
                                    ->setTo($scheduler->getEmailAddress())
                                    ->setBody(
                                        $this->getContainer()->get('templating')
                                            ->render(
                                                '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                                ['body' => $body]
                                            ),
                                        'text/html'
                                    );
                            }else {
                                $message = (new Swift_Message('EasyCloses.com: Transaction documents are missed.'))
                                    ->setFrom('notifications@easycloses.com')
                                    ->setTo($scheduler->getEmailAddress())
                                    ->setBody($this->getContainer()->get('templating')->render(
                                        '@LocalsBestUser/transaction/non-received-docs-email-templates/4.html.twig',
                                        [
                                            'user' => $scheduler->getTransaction()->getCreatedBy(),
                                            'transaction' => $scheduler->getTransaction(),
                                        ]
                                    ), 'text/html');
                            }
                            break;
                    }

                    if ( ($daysPass > 17) && (($daysPass - 17) % 7 == 0) ) {
                        $emailTemplate = $this->em->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
                            [
                                'business' => $business,
                                'category' => 'Non Received Documents',
                                'template_number' => 4
                            ]
                        );

                        if($emailTemplate !== null) {
                            $body = str_replace(
                                [
                                    '*agent_full_name*',
                                    '*transaction_address*',
                                    '*documents_count*',
                                ],
                                [
                                    $scheduler->getTransaction()->getCreatedBy()->getFullName(),
                                    $scheduler->getTransaction()->getFullAddress(),
                                    $docCounter,
                                ],
                                $emailTemplate->getEmailBody()
                            );

                            $message = (new Swift_Message($emailTemplate->getEmailTitle()))
                                ->setFrom('notifications@easycloses.com')
                                ->setTo($scheduler->getEmailAddress())
                                ->setBody(
                                    $this->getContainer()->get('templating')
                                        ->render(
                                            '@LocalsBestUser/transaction/non-received-email-template.html.twig',
                                            ['body' => $body]
                                        ),
                                    'text/html'
                                );
                        }else {
                            $message = (new Swift_Message('EasyCloses.com: Transaction documents are missed.'))
                                ->setFrom('notifications@easycloses.com')
                                ->setTo($scheduler->getEmailAddress())
                                ->setBody($this->getContainer()->get('templating')->render(
                                    '@LocalsBestUser/transaction/non-received-docs-email-templates/4.html.twig',
                                    [
                                        'user' => $scheduler->getTransaction()->getCreatedBy()->getFullName(),
                                        'transaction' => $scheduler->getTransaction(),
                                    ]
                                ), 'text/html');
                        }
                    }

                    $this->getContainer()->get('mailer')->send($message);

                    /** @var User $user */
                    $user = $scheduler->getTransaction()->getCreatedBy();

                    if ($user !== null) {
                        $note = new Note();
                        $note->setStatus($this->em->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus());
                        $note->setNote("NR Email: MLS# " . $scheduler->getTransaction()->getFullAddress())
                            ->setPrivate(true)
                            ->setObjectType('LocalsBestUserBundle:User')
                            ->setObjectId($user->getId())
                            ->setUser($business->getOwner())
                            ->setCreatedBy($business->getOwner());

                        $this->em->persist($note);
                        $this->em->flush();


                        $staff = $this->em
                            ->getRepository('LocalsBestUserBundle:User')
                            ->findStaff($business->getOwner(), $business);
                        if(count($staff)) {
                            foreach ($staff as $staffUser) {
                                $share = new Share();
                                $share->setUser($staffUser);

                                $token = base64_encode(time() . ':' . rand());

                                $share->setToken($token);
                                $share->setObjectType(ObjectTypeType::User);
                                $share->setCreatedBy($business->getOwner());
                                $share->setObjectId($user->getId());

                                $user->addShare($share);
                                $note->addShare($share);
                            }
                        }
                    }
                    $scheduler->setCounter($scheduler->getCounter() + 1);

                    $this->em->flush();
                }
            }
        }

        $output->writeln('Done');
    }
}
