<?php

namespace LocalsBest\UserBundle\Command;

use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\CommonBundle\Entity\Tag;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\DocumentUser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportContactsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('import:contacts')
            ->setDescription('Command will import contacts from LB DB dump file to EC DB.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        include('web/contacts.php');

        $i = 0;
        $batchSize = 100;

        foreach ($contacts as $item) {
            $user = $this->em->getRepository('LocalsBestUserBundle:User')
                ->findOneBy(['portal_user_id' => $item['contact_assigned_to_portal_user_id']]);

            $contact = $this->em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy([
                'createdBy' => $user,
                'firstName' => $item['contact_firstname'],
                'lastName'  => $item['contact_lastname'],
                'email'     => $item['contact_email'],
                'number'    => $item['contact_primary_phone'],
            ]);

            if ($contact === null) {
                $contact = new AllContact();
                $contact->setFirstName($item['contact_firstname']);
                $contact->setLastName($item['contact_lastname']);
                $contact->setNumber($item['contact_primary_phone']);
                $contact->setEmail($item['contact_email']);
                $contact->setCreatedBy($user);

                $this->em->persist($contact);
                $this->em->flush();
            }

            $note = $this->em->getRepository('LocalsBestCommonBundle:Note')->findOneBy([
                'createdBy'  => $user,
                'note'       => $item['contact_notes'],
                'objectType' => 'LocalsBestUserBundle:AllContact',
                'objectId'   => $contact->getId(),
            ]);

            if ($note === null && $item['contact_notes'] != '' && $item['contact_notes'] !== NULL) {
                $note = new Note();
                $note->setCreatedBy($user);
                $note->setCreated(strtotime($item['contact_notes_timestamp']));
                $note->setNote($item['contact_notes']);
                $note->setObjectType('LocalsBestUserBundle:AllContact');
                $note->setObjectId($contact->getId());
                $note->setPrivate(true);
                $contact->addNote($note);

                $this->em->persist($note);
                $this->em->flush();
            }

            $tag = $this->em->getRepository('LocalsBestCommonBundle:Tag')->findOneBy([
                'createdBy'  => $user,
                'tag'        => $item['contact_tags_tagname'],
                'objectType' => 'LocalsBestUserBundle:AllContact',
                'objectId'   => $contact->getId(),
            ]);

            if ($tag === null && $item['contact_tags_tagname'] != '' && $item['contact_tags_tagname'] !== NULL) {
                $tag = new Tag();
                $tag->setCreatedBy($user);
                $tag->setTag($item['contact_tags_tagname']);
                $tag->setObjectType('LocalsBestUserBundle:AllContact');
                $tag->setObjectId($contact->getId());
                $contact->addTag($tag);

                $this->em->persist($tag);
                $this->em->flush();
            }

            $document = $this->em->getRepository('LocalsBestUserBundle:DocumentUser')->findOneBy([
                'createdBy'  => $user,
                'fileName'   => 'contacts/' . $contact->getId() . '/' . $item['attachment_contact_file_name'],
                'allContact' => $contact,
            ]);

            if (
                $document === null
                && $item['attachment_contact_file_name'] != ''
                && $item['attachment_contact_file_name'] !== NULL
            ) {
                $file_original = file_get_contents(
                    str_replace(
                        '/home/localsbest/public_html_live/',
                        'http://localsbest.com/',
                        $item['attachment_contact_file_directory']
                    )
                );
                $document = new DocumentUser();
                $document->setCreatedBy($user);
                $document->setAllContact($contact);

                $fileName = $item['attachment_contact_file_name'];
                $path = $this->getContainer()->get('kernel')->getRootDir().'/../web';
                $filePath = $path.'/'.$fileName;

                file_put_contents($filePath, $file_original);
                $file = new \Symfony\Component\HttpFoundation\File\File($filePath);

                $sdk = $this->get('aws_sdk');
                $s3  = $sdk->createS3();

                $result = $s3->putObject(array(
                    'Bucket' => $this->getContainer()->getParameter('users_bucket'),
                    'Key' => 'contacts/' . $contact->getId() . '/' . $item['attachment_contact_file_name'],
                    'Body' => fopen($filePath, 'r'),
                    'ACL' => 'public-read'
                ));

                $document->setFile($file);
                $document
                    ->setFileName('contacts/' . $contact->getId() . '/' . $item['attachment_contact_file_name'])
                    ->setExtension($file->getExtension());
                $document->setStatus($this->em->getRepository('LocalsBestUserBundle:Document')->getDefaultStatus());

                $this->em->persist($document);
                unlink($filePath);

                $this->em->flush();
            }
            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->em->flush();
    }
}