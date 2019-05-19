<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * LocalsBest\UserBundle\Entity\DepositAlert
 *
 * @ORM\Table(name="deposit_alerts")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\DepositAlertRepository")
 * 
 */
class DepositAlert
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Deposit", inversedBy="alerts")
     * @var type 
     */
    protected $deposit;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    protected $email;
    
    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy HH:mm a"}, nullable = true)
     */
    protected $date;


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
     * Set phone
     *
     * @param boolean $phone
     * @return DepositAlert
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return boolean 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param boolean $email
     * @return DepositAlert
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return boolean 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return DepositAlert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set deposit
     *
     * @param \LocalsBest\UserBundle\Entity\Deposit $deposit
     * @return DepositAlert
     */
    public function setDeposit(\LocalsBest\UserBundle\Entity\Deposit $deposit = null)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return \LocalsBest\UserBundle\Entity\Deposit 
     */
    public function getDeposit()
    {
        return $this->deposit;
    }
    }
