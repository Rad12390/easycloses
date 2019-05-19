<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * LocalsBest\UserBundle\Entity\Deposit
 *
 * @ORM\Table(name="deposits")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\DepositRepository")
 * 
 */
class Deposit
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\MoneyBox", inversedBy="deposits")
     * @var type 
     */
    protected $money;
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\DepositAlert", mappedBy="deposit", cascade={"all"}, orphanRemoval=true)
     * @var ArrayCollection
     */
    protected $alerts;
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $label;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    protected $depositAlert;
    
    /**
     * @ORM\Column(type="decimal", precision=11, scale=2, nullable=true)
     */
    protected $amount;
    
    /**
     * @ORM\Column(type="date", nullable=true)
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
     * Set amount
     *
     * @param string $amount
     * @return Deposit
     */
    public function setAmount($amount)
    {
        $amount = str_replace(['$', ','], '', $amount);

        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Deposit
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
     * Set money
     *
     * @param \LocalsBest\UserBundle\Entity\MoneyBox $money
     * @return Deposit
     */
    public function setMoney(\LocalsBest\UserBundle\Entity\MoneyBox $money = null)
    {
        $this->money = $money;

        return $this;
    }

    /**
     * Get money
     *
     * @return \LocalsBest\UserBundle\Entity\MoneyBox 
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Deposit
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    public function setMoneyBox(MoneyBox $moneyBox)
    {
        $this->money = $moneyBox;
        
        return $this;
    }
    
    public function getMoneyBox()
    {
        return $this->money;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection(); 
        //$this->alerts->add(new DepositAlert());
    }

    /**
     * Add alerts
     *
     * @param \LocalsBest\UserBundle\Entity\DepositAlert $alerts
     * @return Deposit
     */
    public function addAlert(\LocalsBest\UserBundle\Entity\DepositAlert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \LocalsBest\UserBundle\Entity\DepositAlert $alerts
     */
    public function removeAlert(\LocalsBest\UserBundle\Entity\DepositAlert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Set depositAlert
     *
     * @param boolean $depositAlert
     * @return Deposit
     */
    public function setDepositAlert($depositAlert)
    {
        $this->depositAlert = $depositAlert;

        return $this;
    }

    /**
     * Get depositAlert
     *
     * @return boolean 
     */
    public function getDepositAlert()
    {
        return $this->depositAlert;
    }
}
