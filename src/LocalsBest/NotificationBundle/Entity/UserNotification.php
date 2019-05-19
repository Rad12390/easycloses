<?php

namespace LocalsBest\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * The User Notification entity
 *
 * @ORM\Table(name="user_notifications")
 * @ORM\Entity(repositoryClass="LocalsBest\NotificationBundle\Entity\UserNotificationRepository")
 */
class UserNotification
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Connected Notification
     * 
     * @var Notification
     * @ORM\ManyToOne(targetEntity="\LocalsBest\NotificationBundle\Entity\Notification", inversedBy="forUsers", fetch="EAGER")
     */
    protected $notification;
    
    /**
     * @ORM\Column(name="`read`", type="boolean")
     * @var boolean
     */
    protected $read = false;
    
    /**
     * @ORM\Column(name="read_on", type="integer", nullable=true)
     * @var int
     */
    protected $readOn;

    /**
     * @ORM\ManyToOne(targetEntity="\LocalsBest\UserBundle\Entity\User", inversedBy="notifications")
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set read
     *
     * @param boolean $read
     * @return UserNotification
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set readOn
     *
     * @param integer $readOn
     * @return UserNotification
     */
    public function setReadOn($readOn)
    {
        $this->readOn = $readOn;

        return $this;
    }

    /**
     * Get readOn
     *
     * @return integer 
     */
    public function getReadOn()
    {
        return $this->readOn;
    }

    /**
     * Set notification
     *
     * @param \LocalsBest\NotificationBundle\Entity\Notification $notification
     * @return UserNotification
     */
    public function setNotification(\LocalsBest\NotificationBundle\Entity\Notification $notification = null)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return \LocalsBest\NotificationBundle\Entity\Notification 
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return UserNotification
     */
    public function setUser(\LocalsBest\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
