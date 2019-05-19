<?php

namespace LocalsBest\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateContactsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('update:contacts')
            ->setDescription('Command will update contacts status using tags')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $tags = $this->em->getRepository('LocalsBestCommonBundle:Tag')->findBy(
            [
                'tag' => 'closed',
                'deleted' => null,
            ]
        );

        /** @var \LocalsBest\CommonBundle\Entity\Tag $tag */
        foreach ($tags as $tag) {
            if($tag->getObjectType() == 'LocalsBestUserBundle:AllContact') {
                /** @var \LocalsBest\UserBundle\Entity\AllContact $contact */
                $contact = $this->em->getRepository('LocalsBestUserBundle:AllContact')->find($tag->getObjectId());

                if ($contact !== null) {
                    $contact->setIsActive(0);
                    $this->em->flush();
                }
            }
        }

        $output->writeln('done');
    }
}