<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\ShopBundle\Entity\ManageOrderCharities;

/**
 * UserOrder
 *
 * @ORM\Table(name="shop_orders")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\UserOrderRepository")
 */
class UserOrder
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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status = 'unpaid';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="shopOrders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Business
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="shopOrders")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * @var OrderItem
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\OrderItem", mappedBy="order")
     */
    private $shopItems;
    
    /**
     * @var splitHystory
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\SplitPaymentHystory",mappedBy="orderId")
     */
    private $splitHystory;

    /**
     * @var Payment
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Payment", mappedBy="order")
     */
    private $payments;

    /**
     * @ORM\Column(name="stripe_status", type="string", nullable=true)
     */
    private $stripeStatus;

    /**
     * @ORM\Column(name="transaction_id", type="string", nullable=true)
     */
    private $transactionId;

    /**
     * @ORM\Column(name="transfer_group", type="string", nullable=true)
     */
    private $transferGroup;

    /**
     * @ORM\Column(name="stripe_token_id", type="string", nullable=true)
     */
    private $stripeTokenId;

    /**
     * @ORM\Column(name="stripe_order_id", type="string", nullable=true)
     */
    private $stripeOrderId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Destination", mappedBy="order")
     */
    private $destinations;
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\ManageOrderCharities", mappedBy="orderId")
     */
    private $charitiesHistory;


    public function __construct()
    {
        $this->code = uniqid('ec_order_', true);
        $this->shopItems = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->destinations = new ArrayCollection();
    }


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
     * Set code
     *
     * @param string $code
     * @return UserOrder
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserOrder
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
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return UserOrder
     */
    public function setUser(User $user = null)
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

    /**
     * Add shopItems
     *
     * @param \LocalsBest\ShopBundle\Entity\OrderItem $shopItems
     * @return UserOrder
     */
    public function addShopItem(OrderItem $shopItems)
    {
        $this->shopItems[] = $shopItems;

        return $this;
    }

    /**
     * Remove shopItems
     *
     * @param \LocalsBest\ShopBundle\Entity\OrderItem $shopItems
     */
    public function removeShopItem(OrderItem $shopItems)
    {
        $this->shopItems->removeElement($shopItems);
    }

    /**
     * Get shopItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShopItems()
    {
        return $this->shopItems;
    }

    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return UserOrder
     */
    public function setBusiness(Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get splitHystory
     *
     * @return \LocalsBest\ShopBundle\Entity\SplitPaymentHystory 
     */
    public function getSplitHystory()
    {
        return $this->splitHystory;
    }
    /**
     * Set splitHystory
     *
     * @param \LocalsBest\ShopBundle\Entity\SplitPaymentHystory $splitHystory
     * @return UserOrder
     */
    public function setSplitHystory(SplitPaymentHystory $splitHystory = null)
    {
        $this->splitHystory = $splitHystory;

        return $this;
    }
	    
    
    /**
     * Get business
     *
     * @return \LocalsBest\UserBundle\Entity\Business 
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return UserOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->shopItems as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    /**
     * Add payment
     *
     * @param \LocalsBest\ShopBundle\Entity\Payment $payment
     * @return UserOrder
     */
    public function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \LocalsBest\ShopBundle\Entity\Payment $payment
     */
    public function removePayment(Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set stripeStatus
     *
     * @param string $stripeStatus
     * @return UserOrder
     */
    public function setStripeStatus($stripeStatus)
    {
        $this->stripeStatus = $stripeStatus;

        return $this;
    }

    /**
     * Get stripeStatus
     *
     * @return string 
     */
    public function getStripeStatus()
    {
        return $this->stripeStatus;
    }

    /**
     * Set transactionId
     *
     * @param string $transactionId
     * @return UserOrder
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string 
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set transferGroup
     *
     * @param string $transferGroup
     * @return UserOrder
     */
    public function setTransferGroup($transferGroup)
    {
        $this->transferGroup = $transferGroup;

        return $this;
    }

    /**
     * Get transferGroup
     *
     * @return string 
     */
    public function getTransferGroup()
    {
        return $this->transferGroup;
    }

    /**
     * Set stripeTokenId
     *
     * @param string $stripeTokenId
     * @return UserOrder
     */
    public function setStripeTokenId($stripeTokenId)
    {
        $this->stripeTokenId = $stripeTokenId;

        return $this;
    }

    /**
     * Get stripeTokenId
     *
     * @return string 
     */
    public function getStripeTokenId()
    {
        return $this->stripeTokenId;
    }

    /**
     * Set stripeOrderId
     *
     * @param string $stripeOrderId
     * @return UserOrder
     */
    public function setStripeOrderId($stripeOrderId)
    {
        $this->stripeOrderId = $stripeOrderId;

        return $this;
    }

    /**
     * Get stripeOrderId
     *
     * @return string 
     */
    public function getStripeOrderId()
    {
        return $this->stripeOrderId;
    }

    /**
     * Add destinations
     *
     * @param \LocalsBest\ShopBundle\Entity\Destination $destinations
     * @return UserOrder
     */
    public function addDestination(Destination $destinations)
    {
        $this->destinations[] = $destinations;

        return $this;
    }

    /**
     * Remove destinations
     *
     * @param \LocalsBest\ShopBundle\Entity\Destination $destinations
     */
    public function removeDestination(Destination $destinations)
    {
        $this->destinations->removeElement($destinations);
    }

    /**
     * Get destinations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDestinations()
    {
        return $this->destinations;
    }
    
    /**
     * Get splitHystory
     *
     * @return \LocalsBest\ShopBundle\Entity\ManageOrderCharities 
     */
    public function getCharitiesHistory()
    {
        return $this->charitiesHistory;
    }
    /**
     * Set splitHystory
     *
     * @param \LocalsBest\ShopBundle\Entity\ManageOrderCharities $manageOrderCharities
     * @return UserOrder
     */
    public function setCharitiesHistory(ManageOrderCharities $manageOrderCharities = null)
    {
        $this->charitiesHistory = $manageOrderCharities;

        return $this;
    }
}
