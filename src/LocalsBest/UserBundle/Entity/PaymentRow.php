<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentRow
 *
 * @ORM\Table(name="payment_rows")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PaymentRowRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PaymentRow
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
     * @ORM\Column(name="productName", type="string", length=255)
     */
    private $productName;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var PaymentSet
     *
     * @ORM\ManyToOne(targetEntity="PaymentSet", inversedBy="paymentRow")
     * @ORM\JoinColumn(name="paymentSet_id", referencedColumnName="id")
     */
    private $paymentSet;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="setup_fee", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $setupFee = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="productType", type="string", length=64)
     */
    private $productType;

    /**
     * @var integer
     *
     * @ORM\Column(name="productLimit", type="integer", nullable=true)
     */
    private $productLimit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="productUses", type="integer", nullable=true)
     */
    private $productUses;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_service_id", type="string", length=128, nullable=true)
     */
    private $paymentServiceId;


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
     * Set productName
     *
     * @param string $productName
     * @return PaymentRow
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return PaymentRow
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
     * Set quantity
     *
     * @param integer $quantity
     * @return PaymentRow
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
     * @return PaymentRow
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
     * Set productType
     *
     * @param string $productType
     * @return PaymentRow
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * Get productType
     *
     * @return string 
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set productLimit
     *
     * @param integer $productLimit
     * @return PaymentRow
     */
    public function setProductLimit($productLimit)
    {
        $this->productLimit = $productLimit;

        return $this;
    }

    /**
     * Get productLimit
     *
     * @return integer 
     */
    public function getProductLimit()
    {
        return $this->productLimit;
    }

    /**
     * Set productUses
     *
     * @param integer $productUses
     * @return PaymentRow
     */
    public function setProductUses($productUses)
    {
        $this->productUses = $productUses;

        return $this;
    }

    /**
     * Get productUses
     *
     * @return integer 
     */
    public function getProductUses()
    {
        return $this->productUses;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return PaymentRow
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
     * Set product
     *
     * @param \LocalsBest\UserBundle\Entity\Product $product
     * @return PaymentRow
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \LocalsBest\UserBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set paymentSet
     *
     * @param \LocalsBest\UserBundle\Entity\PaymentSet $paymentSet
     * @return PaymentRow
     */
    public function setPaymentSet(PaymentSet $paymentSet = null)
    {
        $this->paymentSet = $paymentSet;

        return $this;
    }

    /**
     * Get paymentSet
     *
     * @return \LocalsBest\UserBundle\Entity\PaymentSet 
     */
    public function getPaymentSet()
    {
        return $this->paymentSet;
    }

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Set paymentServiceId
     *
     * @param string $paymentServiceId
     * @return PaymentRow
     */
    public function setPaymentServiceId($paymentServiceId)
    {
        $this->paymentServiceId = $paymentServiceId;

        return $this;
    }

    /**
     * Get paymentServiceId
     *
     * @return string 
     */
    public function getPaymentServiceId()
    {
        return $this->paymentServiceId;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return PaymentRow
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set setupFee
     *
     * @param string $setupFee
     * @return PaymentRow
     */
    public function setSetupFee($setupFee)
    {
        $this->setupFee = $setupFee;

        return $this;
    }

    /**
     * Get setupFee
     *
     * @return string 
     */
    public function getSetupFee()
    {
        return $this->setupFee;
    }
}
