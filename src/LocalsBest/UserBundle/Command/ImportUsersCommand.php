<?php

namespace LocalsBest\UserBundle\Command;

use LocalsBest\UserBundle\Entity\Address;
use LocalsBest\UserBundle\Entity\Email;
use LocalsBest\UserBundle\Entity\Phone;
use LocalsBest\UserBundle\Entity\Property;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportUsersCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('import:users')
            ->setDescription('Command will import users from xlsx file to EC DB.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        set_time_limit(0);

        $phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject('web/Shred data Table.xlsx');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $business = $this->em->getRepository('LocalsBestUserBundle:Business')->find(179);

        $role = $this->em->getRepository('LocalsBestUserBundle:Role')->find(9);

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowDataSet = $sheet->rangeToArray('A' . $row . ':' . 'J' . $row, NULL, TRUE, FALSE);
            $rowData = $rowDataSet[0];

            foreach($rowData as $key => $item) {
                if($item == 'NULL') {
                    $rowData[$key] = null;
                }
            }

            //  Insert row data array into your database of choice here
            $firstName = $rowData[0] ?: 'empty';
            $lastName = $rowData[1] ?: 'empty';

            $user = new User();
            $user->setFirstName($firstName);
            $user->setLastName($lastName);

            $factory = $this->getContainer()->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword('admin1234', '');

            $user->setPassword($password);
            $user->setUsername($rowData[2]);
            $user->setRole($role);
            $user->setCreatedBy($business->getOwner());

            $email = new Email($rowData[4]);
            $user->getContact()->addEmail($email);

            $phone = new Phone();
            $phone->setNumber($rowData[5]);
            $phone->setType('M');
            $user->getContact()->addPhone($phone);

            $this->em->getRepository('LocalsBestUserBundle:User')->save($user);

            if($rowData[8] !== null) {
                $property = new Property();
                $address = new Address();

                $address->setStreet($rowData[6]);
                $address->setCity($rowData[7]);
                $address->setState($rowData[8]);
                $address->setZip($rowData[9]);

                $this->em->persist($address);

                $property->setAddress($address);
                $property->setTitle($rowData[3]);
                $property->setFormat('Business');
                $property->setType('Commercial');
                $property->setUser($user);

                $this->em->persist($property);
            }
            $user->addBusiness($business);
            $business->addStaff($user);

            $this->em->flush();

            $output->writeln('New Agent: ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ')');
        }
    }
}