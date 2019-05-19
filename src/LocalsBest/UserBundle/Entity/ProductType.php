<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductType
 *
 * @ORM\Table(name="product_types")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ProductTypeRepository")
 */
class ProductType
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
     * @ORM\Column(name="type", type="string", length=127)
     */
    private $type;

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
     * @var int
     *
     * @ORM\Column(name="margin", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "Margin must be at least {{ limit }}",
     * )
     */
    private $margin = 100;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "Value must be at least {{ limit }}",
     * )
     */
    private $value = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="subscription_period", type="integer", nullable=true)
     */
    private $subscriptionPeriod;

    /**
     * @var int
     *
     * @ORM\Column(name="subscription_charges", type="integer", nullable=true)
     */
    private $subscriptionCharges;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="types")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    


    public function __toString()
    {
        return $this->getProduct()->getTitle().' - $'.$this->getPrice().'('.$this->getType().')';
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
     * Set type
     *
     * @param string $type
     * @return ProductType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return ProductType
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
       // $this->productModules = new ArrayCollection();
    }

    /**
     * Set price
     *
     * @param string $price
     * @return ProductType
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
     * Set product
     *
     * @param \LocalsBest\UserBundle\Entity\Product $product
     * @return ProductType
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

    public function getPriceString()
    {
        $delimiter = ' for ';

        if($this->type == 'subscription') {
            return '$' . number_format($this->price * (1 + $this->margin / 100), 0) . $delimiter . $this->getPeriodOfTime();
        } elseif($this->type == 'counter') {
            return '$' . number_format($this->price * (1 + $this->margin / 100), 0) . $delimiter . $this->getUsesCount();
        } else {
            return '$' . number_format($this->price * (1 + $this->margin / 100), 0). $delimiter . ' item';
        }
    }

    private function getPeriodOfTime()
    {
        $suffix = ($this->getSubscriptionPeriod() > 1 ? 's' : '');
        return $this->getSubscriptionPeriod() . ' month' . $suffix;
    }

    private function getUsesCount()
    {
        $suffix = ($this->value > 1 ? 's' : '');
        return $this->value . ' ' . 'use' . $suffix;
    }

    /**
     * Set margin
     *
     * @param integer $margin
     * @return ProductType
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * Get margin
     *
     * @return integer 
     */
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * Set subscriptionPeriod
     *
     * @param integer $subscriptionPeriod
     * @return ProductType
     */
    public function setSubscriptionPeriod($subscriptionPeriod)
    {
        $this->subscriptionPeriod = $subscriptionPeriod;

        return $this;
    }

    /**
     * Get subscriptionPeriod
     *
     * @return integer 
     */
    public function getSubscriptionPeriod()
    {
        return $this->subscriptionPeriod;
    }

    /**
     * Set subscriptionCharges
     *
     * @param integer $subscriptionCharges
     * @return ProductType
     */
    public function setSubscriptionCharges($subscriptionCharges)
    {
        $this->subscriptionCharges = $subscriptionCharges;

        return $this;
    }

    /**
     * Get subscriptionCharges
     *
     * @return integer 
     */
    public function getSubscriptionCharges()
    {
        return $this->subscriptionCharges;
    }

    public function getMarginPrice()
    {
        return $this->getPrice() * (1 + $this->getMargin() / 100);
    }

    /**
     * Set setupFee
     *
     * @param string $setupFee
     * @return ProductType
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
