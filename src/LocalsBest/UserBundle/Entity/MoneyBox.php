<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * LocalsBest\UserBundle\Entity\MoneyBox
 *
 * @ORM\Table(name="money_boxes")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\MoneyBoxRepository")
 * 
 */
class MoneyBox
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Deposit", mappedBy="money", cascade={"all"}, orphanRemoval=true)
     * @var ArrayCollection
     */
    protected $deposits;
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $paymentType;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=2, nullable=false)
     * @Assert\GreaterThan(value="0")
     */
    protected $contractPrice = 0;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    protected $loan;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    protected $referral;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->deposits = new \Doctrine\Common\Collections\ArrayCollection();
        
    }
    
    /**
     * Set paymentType
     *
     * @param string $paymentType
     * @return MoneyBox
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string 
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set loan
     *
     * @param boolean $loan
     * @return MoneyBox
     */
    public function setLoan($loan)
    {
        $this->loan = $loan;

        return $this;
    }

    /**
     * Get loan
     *
     * @return boolean 
     */
    public function getLoan()
    {
        return $this->loan;
    }

    /**
     * Set referral
     *
     * @param boolean $referral
     * @return MoneyBox
     */
    public function setReferral($referral)
    {
        $this->referral = $referral;

        return $this;
    }

    /**
     * Get referral
     *
     * @return boolean 
     */
    public function getReferral()
    {
        return $this->referral;
    }
    
    /**
     * Add deposits
     *
     * @param \LocalsBest\UserBundle\Entity\Deposit $deposits
     * @return MoneyBox
     */
    public function addDeposit(\LocalsBest\UserBundle\Entity\Deposit $deposits)
    {
        $this->deposits[] = $deposits;

        return $this;
    }

    /**
     * Remove deposits
     *
     * @param \LocalsBest\UserBundle\Entity\Deposit $deposits
     */
    public function removeDeposit(\LocalsBest\UserBundle\Entity\Deposit $deposits)
    {
        $this->deposits->removeElement($deposits);
    }

    /**
     * Get deposits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeposits()
    {
        return $this->deposits;
    }


    /**
     * Set contractPrice
     *
     * @param string $contractPrice
     * @return MoneyBox
     */
    public function setContractPrice($contractPrice)
    {
        $this->contractPrice = $this->clearContractPrice($contractPrice);
        return $this;
    }

    /**
     * Get contractPrice
     *
     * @return string 
     */
    public function getContractPrice()
    {
        $price = $this->clearContractPrice($this->contractPrice);
        return ($price > 0) ? ('$' . number_format($price, 0, '.', ',')) : ('');
    }

    protected function clearContractPrice($price)
    {
        $price = str_replace(['$', ',', ' '], '', $price);
        return strlen($price) > 0 ? $price : (int)0;
    }
}
