<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invoice
 *
 * @ORM\Table(name="shop_invoices")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\InvoiceRepository")
 */
class Invoice
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
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=11, scale=2)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_status", type="string", length=255)
     */
    private $paymentStatus = 'pending';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var UserOrder
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", inversedBy="invoice")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $orderId;

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
     * @return Invoice
     */
    public function setAmount($amount)
    {
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
        return $this->amount;
    }

    /**
     * Set paymentStatus
     *
     * @param string $paymentStatus
     * @return Invoice
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return string 
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Invoice
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set orderId
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $orderId
     * @return Invoice
     */
    public function setOrderId(UserOrder $orderId = null)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return \LocalsBest\ShopBundle\Entity\UserOrder 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}
