<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * LocalsBest\UserBundle\Entity\EventAlert
 *
 * @ORM\Table(name="event_alerts")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\EventAlertRepository")
 * 
 */
class EventAlert
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Event", inversedBy="alerts", cascade="persist")
     * @var type 
     */
    protected $event;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy HH:mm a"}, nullable = true)
     */
    protected $date;


    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set phone
     * @param boolean $phone
     * @return EventAlert
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     * @return boolean
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     * @param boolean $email
     * @return EventAlert
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     * @return boolean 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set date
     * @param \DateTime $date
     * @return EventAlert
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set event
     * @param \LocalsBest\UserBundle\Entity\Event $event
     * @return EventAlert
     */
    public function setEvent(\LocalsBest\UserBundle\Entity\Event $event = null)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get event
     * @return \LocalsBest\UserBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
