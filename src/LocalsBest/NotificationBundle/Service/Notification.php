<?php

namespace LocalsBest\NotificationBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\NotificationBundle\Entity;

class Notification
{
    protected $container;
    
    public function setContainer($container)
    {
        $this->container = $container;
        
        return $this;
    }

    /**
     * Create Notification and attache it to user
     *
     * @param string $message
     * @param string $targetPath
     * @param array $targetParams
     * @param array $users
     * @param array $persons
     */
    public function addNotification(
        $message,
        $targetPath,
        array $targetParams = [],
        array $users = [],
        array $persons = []
    )
    {
        // Get Doctrine Entity Manager
        $em = $this->container->get('doctrine')->getManager();
        // Create new Notification
        $notification = new Entity\Notification();
        // Set Params for Notification
        $notification
            ->setMessage($message)
            ->setCreated(time())
            ->setPersons(new ArrayCollection($persons))
            ->setTargetPath($targetPath)
            ->setTargetParams($targetParams);

        // For each User from $users attach new Notification
        foreach ($users as $user) {
            $userNotification = new Entity\UserNotification();
            $userNotification->setUser($user)->setNotification($notification);
            // Save relation
            $em->persist($userNotification);
        }
        // Save Notification
        $em->persist($notification);
        // Update DB
        $em->flush();
    }
}