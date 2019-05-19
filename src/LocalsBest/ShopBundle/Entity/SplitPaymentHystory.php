<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SplitPaymentHystory
 *
 * @ORM\Table(name="shop_split_payment_hystory")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\SplitPaymentHystoryRepository")
 */
class SplitPaymentHystory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder" , inversedBy="splitHystory")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $orderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="charityPercent", type="integer")
     */
    private $charityPercent = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="businessPercent", type="integer")
     */
    private $businessPercent = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="employeePercent", type="integer")
     */
    private $employeePercent = 0;
    
    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=32)
     */
    private $target = 'user';

    /**
     * @var string
     *
     * @ORM\Column(name="targettwo", type="string", length=32)
     */
    private $targettwo = 'user';
    
    /**
     * @var string
     *
     * @ORM\Column(name="rebateStatus", type="string", length=32)
     */
   // private $rebateStatus;

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
     * Set orderId
     *
     * @param integer $orderId
     * @return SplitPaymentHystory
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set charityPercent
     *
     * @param integer $charityPercent
     * @return SplitPaymentHystory
     */
    public function setCharityPercent($charityPercent)
    {
        $this->charityPercent = $charityPercent;

        return $this;
    }

    /**
     * Get charityPercent
     *
     * @return integer 
     */
    public function getCharityPercent()
    {
        return $this->charityPercent;
    }

    /**
     * Set businessPercent
     *
     * @param integer $businessPercent
     * @return SplitPaymentHystory
     */
    public function setBusinessPercent($businessPercent)
    {
        $this->businessPercent = $businessPercent;

        return $this;
    }

    /**
     * Get businessPercent
     *
     * @return integer 
     */
    public function getBusinessPercent()
    {
        return $this->businessPercent;
    }

    /**
     * Set employeePercent
     *
     * @param integer $employeePercent
     * @return SplitPaymentHystory
     */
    public function setEmployeePercent($employeePercent)
    {
        $this->employeePercent = $employeePercent;

        return $this;
    }

    /**
     * Get employeePercent
     *
     * @return integer 
     */
    public function getEmployeePercent()
    {
        return $this->employeePercent;
    }
     /**
     * Set target
     *
     * @param string $target
     * @return PaymentSplitSettings
     */
    public function setTarget($target) {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Set target
     *
     * @param string $targettwo
     * @return PaymentSplitSettings
     */
    public function setTargetTwo($targettwo) {
        $this->targettwo = $targettwo;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTargetTwo() {
        return $this->targettwo;
    }
    
//    public function setRebateStatus($rebateStatus) {
//        $this->rebateStatus = $rebateStatus;
//
//        return $this;
//    }
//
//    /**
//     * Get target
//     *
//     * @return string 
//     */
//    public function getRebateStatus() {
//        return $this->rebateStatus;
//    }
}
