<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrderItem
 *
 * @ORM\Table(name="shop_order_items")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\OrderItemRepository")
 */
class OrderItem
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
     * @var UserOrder
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", inversedBy="shopItems")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var Sku
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Sku", inversedBy="shopItems")
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id")
     */
    private $sku;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2)
     */
    private $price;
    /**
     * @var integer
     *
      * @ORM\Column(name="rebatePercent", type="integer")
     */
    private $rebatePercent = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="markup", type="integer")
     */
    private $markup = 0;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Price", inversedBy="shopItems")
     * @ORM\JoinColumn(name="price_object_id", referencedColumnName="id")
     */
    private $priceObject;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="refunded", type="boolean", options={"default"=false})
     */
    private $refunded = false;

    /**
     * @var array
     *
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    private $options;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $txnid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $orderid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $objecttype;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $applicationfee;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $productid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $subscriptionstatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="subscription_ended_at", type="datetime", nullable=true)
     */
    private $subscriptionEndedAt;

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
     * Set quantity
     *
     * @param integer $quantity
     * @return OrderItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return OrderItem
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
     * Set price
     *
     * @param string $price
     * @return OrderItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return OrderItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set order
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $order
     * @return OrderItem
     */
    public function setOrder(UserOrder $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \LocalsBest\ShopBundle\Entity\UserOrder 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return OrderItem
     */
    public function setSku(Sku $sku = null)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get skuId
     *
     * @return \LocalsBest\ShopBundle\Entity\Sku 
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return OrderItem
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getTotal() {
        return $this->quantity * $this->price;
    }

    /**
     * Set priceObject
     *
     * @param \LocalsBest\ShopBundle\Entity\Price $priceObject
     * @return OrderItem
     */
    public function setPriceObject(\LocalsBest\ShopBundle\Entity\Price $priceObject = null)
    {
        $this->priceObject = $priceObject;

        return $this;
    }

    /**
     * Get priceObject
     *
     * @return \LocalsBest\ShopBundle\Entity\Price 
     */
    public function getPriceObject()
    {
        return $this->priceObject;
    }

    /**
     * Set refunded
     *
     * @param boolean $refunded
     * @return OrderItem
     */
    public function setRefunded($refunded)
    {
        $this->refunded = $refunded;

        return $this;
    }

    /**
     * Get refunded
     *
     * @return boolean 
     */
    public function getRefunded()
    {
        return $this->refunded;
    }

    /**
     * @return string
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @param string $markup
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;
    }
    /**
     * @return string
     */
    public function getRebatePercent()
    {
        return $this->rebatePercent;
    }

    /**
     * @param string $rebatePercent
     */
    public function setRebatePercent($rebatePercent)
    {
        $this->rebatePercent = $rebatePercent;
    }

    /**
     * Set txnid
     *
     * @param string $txnid
     * @return OrderItem
     */
    public function setTxnid($txnid)
    {
        $this->txnid = $txnid;

        return $this;
    }

    /**
     * Get txnid
     *
     * @return string 
     */
    public function getTxnid()
    {
        return $this->txnid;
    }

    /**
     * Set objecttype
     *
     * @param string $objecttype
     * @return OrderItem
     */
    public function setObjecttype($objecttype)
    {
        $this->objecttype = $objecttype;

        return $this;
    }

    /**
     * Get objecttype
     *
     * @return string 
     */
    public function getObjecttype()
    {
        return $this->objecttype;
    }

    /**
     * Set applicationfee
     *
     * @param string $applicationfee
     * @return OrderItem
     */
    public function setApplicationfee($applicationfee)
    {
        $this->applicationfee = $applicationfee;

        return $this;
    }

    /**
     * Get applicationfee
     *
     * @return string 
     */
    public function getApplicationfee()
    {
        return $this->applicationfee;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return OrderItem
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

    /**
     * Set subscriptionstatus
     *
     * @param string $subscriptionstatus
     * @return OrderItem
     */
    public function setSubscriptionstatus($subscriptionstatus)
    {
        $this->subscriptionstatus = $subscriptionstatus;

        return $this;
    }

    /**
     * Get subscriptionstatus
     *
     * @return string 
     */
    public function getSubscriptionstatus()
    {
        return $this->subscriptionstatus;
    }

    /**
     * Set productid
     *
     * @param string $productid
     * @return OrderItem
     */
    public function setProductid($productid)
    {
        $this->productid = $productid;

        return $this;
    }

    /**
     * Get productid
     *
     * @return string 
     */
    public function getProductid()
    {
        return $this->productid;
    }

    /**
     * Set orderid
     *
     * @param string $orderid
     * @return OrderItem
     */
    public function setOrderid($orderid)
    {
        $this->orderid = $orderid;

        return $this;
    }

    /**
     * Get orderid
     *
     * @return string 
     */
    public function getOrderid()
    {
        return $this->orderid;
    }

    /**
     * @return \DateTime
     */
    public function getSubscriptionEndedAt()
    {
        return $this->subscriptionEndedAt;
    }

    /**
     * @param \DateTime $subscriptionEndedAt
     */
    public function setSubscriptionEndedAt($subscriptionEndedAt)
    {
        $this->subscriptionEndedAt = $subscriptionEndedAt;
    }
}
