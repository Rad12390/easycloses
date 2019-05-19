<?php

namespace LocalsBest\UserBundle\Command;

use LocalsBest\UserBundle\Entity\AssociationRow;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class AddBoardIdsCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('add-associations-ids')
            ->setDescription('Command will add Associations IDs to NY users')
        ;
    }

    /**
     * Command that we used for one time
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        include('web/member_categories.php');
        $i = 0;
        $batchSize = 100;

        foreach ($member_categories as $item) {
            $this->em->getFilters()->enable('softdeleteable');
            $this->em->getFilters()->disable('softdeleteable');
            $user = $this->em->getRepository('LocalsBestUserBundle:User')
                ->findOneBy(['portal_user_id' => $item['portal_user_id']]);

            if($user === null) {
                $output->writeln($i . ' ERROR! There is no user with portal ID: ' . $item['portal_user_id']);
                continue;
            }

            $association = $this->em->getRepository('LocalsBestUserBundle:Association')->find(9);
            $assocRow = $this->em->getRepository('LocalsBestUserBundle:AssociationRow')
                ->findOneBy(['user' => $user, 'association' => $association]);
            $persist = false;

            if($assocRow == null) {
                $assocRow = new AssociationRow();
                $assocRow->setAssociation($association);
                $assocRow->setUser($user);

                $persist = true;
            }
            $assocRow->setAssociationMlsId($item['msl_board_id']);

            if($persist == true) {
                $this->em->persist($assocRow);
            }
            $this->em->flush();
            $output->writeln(
                $i . ' COMPLETE! Add #Association ID for users with portal ID: ' . $item['portal_user_id']
                . ' (' . $item['msl_board_id'] . ')'
            );

            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->em->flush();
    }
}