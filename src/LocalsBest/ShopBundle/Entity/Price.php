<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Price
 *
 * @ORM\Table(name="shop_prices")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\PriceRepository")
 */
class Price
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

     /**
     * @var string
     *
     * @ORM\Column(name="retailPrice", type="decimal", precision=11, scale=2)
     */
    private $retailPrice;
    
    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=11, scale=2)
     */
    private $amount;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="rebate", type="integer")
     */
    private $rebate;



    /**
     * @var string
     *
     * @ORM\Column(name="subscription_type", type="string", nullable=true)
     */
    private $subscriptionType;

    /**
     * @var string
     *
     * @ORM\Column(name="stripe_planid", type="string", nullable=true)
     */
    private $stripeplanid;

    /**
     * @var string
     *
     * @ORM\Column(name="stripe_productid", type="string", nullable=true)
     */
    private $stripeproductid;

    /**
     * @ORM\ManyToOne(targetEntity="Sku", inversedBy="prices", cascade={"persist"})
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $sku;

    public function __toString()
    {
        return '$' . $this->amount;
    }

    public function __clone()
    {
        $this->id = null;
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
     * @return Price
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
     * Set amount
     *
     * @param string $amount
     * @return Price
     */
    public function setretailPrice($retailPrice)
    {
        $this->retailPrice = $retailPrice;

        return $this;
    }

    /**
     * Get retailPrice
     *
     * @return string 
     */
    public function getretailPrice()
    {
        return $this->retailPrice;
    }

    /**
     * Set retailPrice
     *
     * @param string $amount
     * @return Price
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
    
    /*  * Set rebate
     *
     * @param string $rebate
     * @return rebate
     */
    public function setRebate($rebate)
    {
        $this->rebate = $rebate;

        return $this;
    }

    /**
     * Get rebate
     *
     * @return string 
     */
    public function getRebate()
    {
        return $this->rebate;
    }


    /**
     * Set sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return Price
     */
    public function setSku(Sku $sku = null)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return \LocalsBest\ShopBundle\Entity\Sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->subscriptionType;
    }

    /**
     * @param string $subscriptionType
     */
    public function setSubscriptionType($subscriptionType)
    {
        $this->subscriptionType = $subscriptionType;
    }

    /**
     * Set stripeplanid
     *
     * @param string $stripeplanid
     * @return Price
     */
    public function setStripeplanid($stripeplanid)
    {
        $this->stripeplanid = $stripeplanid;

        return $this;
    }

    /**
     * Get stripeplanid
     *
     * @return string 
     */
    public function getStripeplanid()
    {
        return $this->stripeplanid;
    }

    /**
     * Set stripeproductid
     *
     * @param string $stripeproductid
     * @return Price
     */
    public function setStripeproductid($stripeproductid)
    {
        $this->stripeproductid = $stripeproductid;

        return $this;
    }

    /**
     * Get stripeproductid
     *
     * @return string 
     */
    public function getStripeproductid()
    {
        return $this->stripeproductid;
    }

    public function getRetailAmount($markup)
    {
        return (1 + $markup /100) * $this->getAmount();
    }

    /**
     * @Assert\Callback
     */
    public function checkType(ExecutionContext $context)
    {
        if (
            $this->type == 'subscription'
            && (
                $this->subscriptionType == ''
                || $this->subscriptionType === null
                //|| $this->subscriptionInterval == ''
                //|| $this->subscriptionInterval === null
            )
        ) {
            $context->addViolation('You have to add Subscription Type  for Subscription Price!', [], null);
        }
    }
}
