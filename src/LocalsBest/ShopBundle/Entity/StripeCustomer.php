<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LocalsBest\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\StripeCustomerRepository")
 * @ORM\Table(name="shop_stripe_customers")
 */
class StripeCustomer
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="customer_name", type="string", length=128)
     */
    private $customerName;

    /**
     * @ORM\Column(name="customer_email", type="string", length=128)
     */
    private $customerEmail;

    /**
     * @ORM\Column(name="stripe_account_id", type="string", nullable=true)
     */
    private $stripeAccountId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="stripeCustomers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="stripeWholesalers")
     * @ORM\JoinColumn(name="wholesaler_id", referencedColumnName="id")
     */
    private $wholesaler;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

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
     * Set customerName
     *
     * @param string $customerName
     * @return StripeCustomer
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * Get customerName
     *
     * @return string 
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Set customerEmail
     *
     * @param string $customerEmail
     * @return StripeCustomer
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * Get customerEmail
     *
     * @return string 
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Set stripeAccountId
     *
     * @param string $stripeAccountId
     * @return StripeCustomer
     */
    public function setStripeAccountId($stripeAccountId)
    {
        $this->stripeAccountId = $stripeAccountId;

        return $this;
    }

    /**
     * Get stripeAccountId
     *
     * @return string 
     */
    public function getStripeAccountId()
    {
        return $this->stripeAccountId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return StripeCustomer
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
     * @return StripeCustomer
     */
    public function setUser(\LocalsBest\UserBundle\Entity\User $user = null)
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
     * Set wholesaler
     *
     * @param \LocalsBest\UserBundle\Entity\User $wholesaler
     * @return StripeCustomer
     */
    public function setWholesaler(\LocalsBest\UserBundle\Entity\User $wholesaler = null)
    {
        $this->wholesaler = $wholesaler;

        return $this;
    }

    /**
     * Get wholesaler
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getWholesaler()
    {
        return $this->wholesaler;
    }
}
