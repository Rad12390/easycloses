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
 * @ORM\Table(name="listing_money_boxes")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ListingMoneyBoxRepository")
 * 
 */
class ListingMoneyBox
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     * @ORM\Column(type="decimal", precision=11, scale=2, nullable=false)
     */
    protected $contractPrice = 0;
    
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
     * Set contractPrice
     *
     * @param string $contractPrice
     * @return ListingMoneyBox
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
        return $price>0 ? '$' . number_format($price, 0, '.', ',') : '';
    }

    /**
     * Set referral
     *
     * @param boolean $referral
     * @return ListingMoneyBox
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

    protected function clearContractPrice($price)
    {
        $price = str_replace(['$', ',', ' '], '', $price);
        return strlen($price) > 0 ? $price : (int)0;
    }
}
