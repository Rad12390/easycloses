<?php

namespace LocalsBest\NotificationBundle\Service;

use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\Document;
use LocalsBest\UserBundle\Entity\DocumentType;
use LocalsBest\UserBundle\Entity\DocumentUser;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Invite;
use LocalsBest\UserBundle\Entity\Job;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\Support;
use LocalsBest\UserBundle\Entity\Transaction;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Entity\Vendor;
use Swift_Attachment;
use Swift_Message;

class MailMan
{
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    public function sendMail(
        $subject,
        $template,
        $templateParams = [],
        $recipients = [],
        $fromAddress = null,
        $fromName = null
    )
    {
        if (!$fromAddress) {
            $fromAddress = $this->container->getParameter('mailer_from_address');
        }

        if (!$fromName) {
            $fromName = $this->container->getParameter('mailer_from_name');
        }

        $message = (new Swift_Message($subject))
            ->setFrom($fromAddress, $fromName)
            ->setTo($recipients)
            ->setBody(
            $this->container->get('templating')->render(
                $template,
                $templateParams
            ), 'text/html');

        return $this->container->get('mailer')->send($message);
    }

    public function paymentErrorMail($info, $recipient)
    {
        return $this->sendMail(
            '[FAILED PAYMENT] New User Payment Issue',
            '@LocalsBestNotification/mails/error-payment.html.twig',
            [
                'info' => $info,
            ],
            [$recipient]
        );
    }

    /**
     * @param Event $event
     * @param User $user
     * @param string $message
     *
     * @return mixed
     */
    public function eventAlertMail(Event $event, $user, $message)
    {
        return $this->sendMail(
            'Alert for Event "' . $event->getTitle() . '"',
            '@LocalsBestNotification/mails/alert-for-event.html.twig',
            array('user' => $user, 'message' => $message),
            array($event->getCreatedBy()->getPrimaryEmail()->getEmail())
        );
    }

    /**
     * @param User $agent
     * @param string $message
     *
     * @return mixed
     */
    public function assignContactsMail($agent, $message)
    {
        return $this->sendMail(
            'You have new Contacts',
            '@LocalsBestNotification/mails/assign-contacts.html.twig',
            array('message' => $message),
            array($agent->getPrimaryEmail()->getEmail())
        );
    }

    /**
     * @param Event $event
     * @param User $user
     *
     * @return mixed
     */
    public function listingExpiredMail(Event $event, User $user)
    {
        return $this->sendMail(
            'Expiring Listing "' . $event->getListing()->getTransaction()->getFullAddress() . '"',
            '@LocalsBestNotification/mails/expire-listing.html.twig',
            array('user' => $user, 'event' => $event),
            array($user->getPrimaryEmail()->getEmail())
        );
    }

    /**
     * @param Invite $invite
     *
     * @return mixed
     */
    public function sendInvitationMail(Invite $invite)
    {
        return $this->sendMail(
            'Membership Notifications',
            '@LocalsBestNotification/mails/invite.html.twig',
            array('token' => $invite->getToken()),
            array($invite->getEmail())
        );
    }

    /**
     * @param Vendor $vendor
     * @param string $token
     * @param User $user
     *
     * @return mixed
     */
    public function sendVendorCreateMail(Vendor $vendor, $token, User $user)
    {
        return $this->sendMail(
            $vendor->getContactName() . ' A great way to get more jobs - join my free service directory',
            '@LocalsBestNotification/mails/vendor_create.html.twig',
            ['token' => $token, 'vendor' => $vendor],
            [$vendor->getEmail()],
            $user->getPrimaryEmail()->getEmail(),
            $user->getFullName() . ' ' . $user->getBusinesses()->first()->getName()
        );
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function sendPasswordResetMail(User $user)
    {
        return $this->sendMail(
            'Password Reset',
            '@LocalsBestNotification/mails/password-reset.html.twig',
            array('user' => $user),
            array($user->getPrimaryEmail()->getEmail())
        );
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function sendWelcomeMail(User $user)
    {
        return $this->sendMail(
            'Your registration at EasyCloses.com',
            '@LocalsBestNotification/mails/welcome.html.twig',
            array('user' => $user),
            array($user->getPrimaryEmail()->getEmail())
        );
    }

    public function sendASSignWelcomeMail(User $user)
    {
        return $this->sendMail(
            'Thank you for registering with AS Signs and Services!',
            '@LocalsBestNotification/mails/as-signs-welcome.html.twig',
            array('user' => $user),
            array($user->getPrimaryEmail()->getEmail())
        );
    }

    /**
     * @param User $user
     * @param Share $share
     * @param Document $document
     *
     * @return mixed
     */
    public function documentReadRecieptMail(User $user, Share $share, Document $document)
    {
        return $this->sendMail(
            'Document Read-Receipt',
            '@LocalsBestNotification/mails/readReciept.html.twig',
            array('share' => $share, 'document' => $document),
            array($user->getPrimaryEmail()->getEmail())
        );
    }

    /**
     * @param Support $service
     *
     * @return mixed
     */
    public function sendServiceMail(Support $service)
    {
        return $this->sendMail(
            'New Service Requested',
            '@LocalsBestNotification/mails/service.html.twig',
            array('service' => $service),
            array('assistant@mylocalsbest.com')
        );
    }

    /**
     * @param Share $share
     * @param Note|null $note
     */
    public function sendSharedMail(Share $share, $note = null)
    {
        $em = $this->container->get('doctrine')->getManager();
        if ($share->getObjectType() === ObjectTypeType::Event) {
            $event = $em->getRepository('LocalsBestUserBundle:Event')->find($share->getObjectId());

            return $this->sendMail(
                'Shared Event',
                '@LocalsBestNotification/mails/share-event.html.twig',
                array('share' => $share, 'event' => $event),
                array($share->getUser()->getPrimaryEmail()->getEmail()),
                $share->getCreatedBy()->getPrimaryEmail()->getEmail(),
                $share->getCreatedBy()->getFullName()
            );
        } elseif (
            $share->getObjectType() === ObjectTypeType::DocumentSingle || $share->getObjectType() === ObjectTypeType::DocumentUser || $share->getObjectType() === ObjectTypeType::DocumentJob || $share->getObjectType() === ObjectTypeType::Document
        ) {
            return $this->sendMail(
                'Shared Document',
                '@LocalsBestNotification/mails/share-document.html.twig',
                array('share' => $share),
                array($share->getUser()->getPrimaryEmail()->getEmail()),
                $share->getCreatedBy()->getPrimaryEmail()->getEmail(),
                $share->getCreatedBy()->getFullName()
            );
        } elseif ($share->getObjectType() === ObjectTypeType::User) {
            return $this->sendMail(
                'Shared User Note',
                '@LocalsBestNotification/mails/share-note-user.html.twig',
                array('share' => $share),
                array($share->getUser()->getPrimaryEmail()->getEmail())
            );
        } elseif ($share->getObjectType() === ObjectTypeType::Transaction) {
            /** @var Transaction $transaction */
            $transaction = $em->getRepository('LocalsBestUserBundle:Transaction')->find($share->getObjectId());
            return $this->sendMail(
                'New Transaction Note for "' . $transaction->getFullAddress() . '"',
                '@LocalsBestNotification/mails/share-note-transaction.html.twig',
                array('share' => $share, 'note' => $note),
                array($share->getUser()->getPrimaryEmail()->getEmail())
            );
        }
    }

    /**
     * @param AllContact $contact
     * @param Note $note
     * @param User $user
     *
     * @return mixed
     */
    public function sendNoteMailToContact(AllContact $contact, Note $note, User $user)
    {
        return $this->sendMail(
            'New Note for ' . $contact->getFirstName() . ' ' . $contact->getLastName(),
            '@LocalsBestNotification/mails/share-note-contact.html.twig',
            array('contact' => $contact, 'note' => $note, 'user' => $user),
            array($contact->getEmail())
        );
    }

    /**
     * @param User $vendor
     * @param Job $job
     * @param Note|null $note
     *
     * @return mixed
     */
    public function sendMailToVendorAboutNewJob(User $vendor, Job $job, $note = null)
    {
        return $this->sendMail(
            'New Quote or Job Request!',
            '@LocalsBestNotification/mails/vendor-new-job.html.twig',
            ['vendor' => $vendor, 'job' => $job, 'note' => $note],
            [$vendor->getPrimaryEmail()->getEmail()]
        );
    }

    /**
     * App will send email to AS Sign about new Job
     *
     * @param User $vendor
     * @param Job $job
     * @param null $note
     * @return mixed
     */
    public function sendAsSignMailToVendorAboutNewJob(User $vendor, Job $job, $note = null)
    {
        return $this->sendMail(
            'New Quote or Job Request!',
            '@LocalsBestNotification/mails/as-sign-new-job.html.twig',
            ['vendor' => $vendor, 'job' => $job, 'note' => $note],
            [$vendor->getPrimaryEmail()->getEmail()]
        );
    }

    /**
     * @param $user
     * @param $data
     * @param $documentTypes
     *
     * @return mixed
     */
    public function sendDocuments($user, $data, $documentTypes)
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $fromAddress = $this->container->getParameter('mailer_from_address');
        $fromName = $this->container->getParameter('mailer_from_name');

        $bcc = [];
        $cc = [];

        if ($data['toCc'] !== '') {
            $cc = explode(',', $data['toCc']);
        }

        if ($data['toBcc'] !== '') {
            $bcc = explode(',', $data['toBcc']);
        }

        $message = new Swift_Message($data['subject']);
        $message->setFrom($fromAddress, $fromName)
            ->setTo($data['to'])
            ->setBody(
                $this->container->get('templating')->render(
                    '@LocalsBestNotification/mails/email-with-documents.html.twig',
                    [
                        'message' => $data['message'],
                        'user' => $user,
                    ]
                ),
                'text/html'
            )
        ;

        if (count($bcc) > 0) {
            $message->setBcc($bcc);
        }

        if (count($cc) > 0) {
            $message->setCc($cc);
        }

        /** @var DocumentType $item */
        foreach ($documentTypes as $item) {
            $path = $helper->asset($item->getDocument(), 'file');
            $filename = $item->getName() . '.' . $item->getDocument()->getExtension();
            $message->attach(Swift_Attachment::fromPath($path)->setFilename($filename));
        }

        return $this->container->get('mailer')->send($message);
    }

    /**
     * @param $user
     * @param $data
     * @param $documents
     *
     * @return mixed
     */
    public function sendUserDocuments($user, $data, $documents)
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $fromAddress = $this->container->getParameter('mailer_from_address');
        $fromName = $this->container->getParameter('mailer_from_name');

        $bcc = [];
        $cc = [];

        if ($data['toCc'] !== '') {
            $cc = explode(',', $data['toCc']);
        }

        if ($data['toBcc'] !== '') {
            $bcc = explode(',', $data['toBcc']);
        }

        $message = new Swift_Message($data['subject']);
        $message->setFrom($fromAddress, $fromName)
            ->setTo($data['to'])
            ->setBody(
                $this->container->get('templating')->render(
                    '@LocalsBestNotification/mails/email-with-documents.html.twig',
                    [
                        'message' => $data['message'],
                        'user' => $user,
                    ]
                ),
                'text/html'
        );

        if (count($bcc) > 0) {
            $message->setBcc($bcc);
        }

        if (count($cc) > 0) {
            $message->setCc($cc);
        }

        /** @var DocumentUser $item */
        foreach ($documents as $item) {

            $filename = last(explode('/', $item->getFileName()));

            $path = $helper->asset($item, $data['requestType']);
            $message->attach(Swift_Attachment::fromPath($path)->setFilename($filename));
        }

        $result = $this->container->get('mailer')->send($message);

        return $result;
    }

    /**
     * Send email with Shredding PDF file
     *
     * @param $recipient
     * @param $pdf
     *
     * @return mixed
     */
    public function sendShreddingPdf($recipient, $pdf, $body, $title = "Shredding PDF")
    {
        // create Vich Uploader object
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

        // get information about sender
        $fromAddress = 'mike@championshredding.com';
        $fromName = $this->container->getParameter('mailer_from_name');

        // Create email
        $message = new Swift_Message($title);
        $message->setFrom($fromAddress, $fromName)
            ->setTo($recipient)
            ->setBody(
                $this->container->get('templating')->render(
                    '@LocalsBestNotification/mails/shredding-pdf.html.twig',
                    ['body' => $body]
                ),
                'text/html'
            )
        ;
        // get PDF file path
        $path = $helper->asset($pdf, 'file');

        // attach PDF to email
        $message->attach(Swift_Attachment::fromPath($path)->setFilename($pdf->getFilename()));

        // return result of email send
        return $this->container->get('mailer')->send($message);
    }

    public function sendNewManagerRegister(User $manager, User $agent, $credentials)
    {
        // get information about sender
        $fromAddress = $this->container->getParameter('mailer_from_address');
        $fromName = $this->container->getParameter('mailer_from_name');

        // Create email
        $message = new Swift_Message('Registration for EC');
        $message
            ->setFrom($fromAddress, $fromName)
            ->setTo($manager->getPrimaryEmail()->getEmail())
            ->setBody(
                $this->container->get('templating')->render(
                    '@LocalsBestNotification/mails/new-manager-register.html.twig',
                    [
                        'manager' => $manager,
                        'agent' => $agent,
                        'credentials' => $credentials
                    ]
                ),
                'text/html'
            )
        ;

        // return result of email send
        return $this->container->get('mailer')->send($message);
    }

    /**
     * Send email with Custom Order from User
     *
     * @param $data
     * @return mixed
     */
    public function customOrderMail($data)
    {
        // Get info about who sent email
        $fromAddress = $this->container->getParameter('mailer_from_address');
        $fromName = $this->container->getParameter('mailer_from_name');

        // Create email object
        $message = new Swift_Message('Custom Order From EC Shop');
        $message
            ->setFrom($fromAddress, $fromName)
            ->setTo('assistant@mylocalsbest.com')
            ->setBody(
                $this->container->get('templating')->render(
                    '@LocalsBestNotification/mails/custom-order.html.twig',
                    ['data' => $data]
                ),
                'text/html'
            )
        ;

        // return result of email send
        return $this->container->get('mailer')->send($message);
    }

    /**
     * App will send this email when Job will get status "closed"
     *
     * @param Job $job
     * @return mixed
     */
    public function closedJobMail(Job $job)
    {
        // Get info about who sent email
        $fromAddress = $this->container->getParameter('mailer_from_address');
        $fromName = $this->container->getParameter('mailer_from_name');

        // Create email object
        $message = new Swift_Message('Job Completed (' . $job->getFullAddress() . ')');
        $message
            ->setFrom($fromAddress, $fromName)
            ->setTo($job->getCreatedBy()->getPrimaryEmail()->getEmail())
            ->setBody(
                $this->container->get('templating')->render(
                    '@LocalsBestNotification/mails/job-closed.html.twig',
                    [
                        'job' => $job,
                        'vendor' => $job->getVendor(),
                    ]
                ),
                'text/html'
            )
        ;

        // return result of email send
        return $this->container->get('mailer')->send($message);
    }

    /**
     * @param Support $service
     *
     * @return mixed
     */
    public function sendContactUsMail($contactUs)
    {
        return $this->sendMail(
                'New Contact Us Requested',
                '@LocalsBestNotification/mails/contact-us.html.twig',
                array('entity' => $contactUs),
                array('support@easycloses.com')
        );
    }

    /**
     * @return mixed
     */
    public function sendSkuContactUsMail($contactUs, $senderEmail = array())
    {
        return $this->sendMail(
            'New Contact Us Requested',
            '@LocalsBestNotification/mails/sku-contact-us.html.twig',
            array('entity' => $contactUs),
            $senderEmail
        );
    }

    /**
     * Send Email to Client if Shop Item have disposition for it
     *
     * @param $params
     *
     * @return mixed
     */
    public function sendDispositionEmailToClient($params)
    {
        $recipient = $params['user'];
        $sku = $params['sku'];
        //  dd($params['shop_items']);
        $items = [];
        $item_quantity = [];
        foreach ($params['shop_items'] as $shop_items) {
            array_push($items, $shop_items->getItem()->getTitle());
            array_push($item_quantity, $shop_items->getQuantity());
        }

        foreach ($sku->getPrices() as $price) {
            $price = $price->getretailPrice();
        }

        $params['price'] = $price;
        $params['items'] = $items;
        $params['item_quantity'] = $item_quantity;

        if ($price > 0) {
            return $this->sendMail(
                'Order Confirmation for "' . $sku->getPackage()->getTitle() . '"',
                '@LocalsBestNotification/mails/disposition-client-email.html.twig',
                $params,
                array($recipient->getPrimaryEmail()->getEmail())
            );
        } else {
            return $this->sendMail(
                'Order Confirmation for "' . $sku->getPackage()->getTitle() . '"',
                '@LocalsBestNotification/mails/disposition-client-email.html.twig',
                $params,
                array($recipient->getPrimaryEmail()->getEmail())
            );
        }
    }

    public function sendInfoEmailToClient($client, $item, $business)
    {
        return $this->sendMail(
            'Information from ' . $business->getName(),
            '@LocalsBestNotification/mails/disposition-info-email.html.twig',
            ['client' => $client, 'business' => $business, 'item' => $item],
            array($client->getPrimaryEmail()->getEmail()),
            'shop@localsbest.com'
        );
    }

    /**
     * Send Email to Client if Shop Item have disposition for it
     *
     * @param $params
     *
     * @return mixed
     */
    public function sendDispositionEmailToVendor($params)
    {

        $recipient = $params['vendor'];
        $sku = $params['sku'];
        //get package title

        $items = [];
        $item_quantity = [];
        foreach ($params['shop_items'] as $shop_items) {
            array_push($items, $shop_items->getItem()->getTitle());
            array_push($item_quantity, $shop_items->getQuantity());
        }

        foreach ($sku->getPrices() as $price) {
            $price = $price->getretailPrice();
        }

        $params['price'] = $price;
        $params['items'] = $items;
        $params['item_quantity'] = $item_quantity;

        if ($price > 0) {
            return $this->sendMail(
                'Order Confirmation for "' . $sku->getPackage()->getTitle() . '"',
                '@LocalsBestNotification/mails/disposition-vendor-email.html.twig',
                $params,
                array($recipient->getPrimaryEmail()->getEmail())
            );
        } else {
            return $this->sendMail(
                'Order Confirmation for "' . $sku->getPackage()->getTitle() . '"',
                '@LocalsBestNotification/mails/disposition-vendor-email.html.twig',
                $params,
                array($recipient->getPrimaryEmail()->getEmail())
            );
        }
    }
}
