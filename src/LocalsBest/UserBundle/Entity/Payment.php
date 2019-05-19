<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Payment
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
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $price_level;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $oath_subscription_id;

    /**
     * @ORM\Column(type="string", length=64, unique=true, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $business_type;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="PaidInvite", mappedBy="payment")
     */
    private $paid_invite;


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
     * Set status
     *
     * @param string $status
     * @return Payment
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
     * Set amount
     *
     * @param string $amount
     * @return Payment
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Payment
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Payment
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Add user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return Payment
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set paid_invite
     *
     * @param \LocalsBest\UserBundle\Entity\PaidInvite $paidInvite
     * @return Payment
     */
    public function setPaidInvite(\LocalsBest\UserBundle\Entity\PaidInvite $paidInvite = null)
    {
        $this->paid_invite = $paidInvite;

        return $this;
    }

    /**
     * Get paid_invite
     *
     * @return \LocalsBest\UserBundle\Entity\PaidInvite 
     */
    public function getPaidInvite()
    {
        return $this->paid_invite;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPriceLevel()
    {
        return $this->price_level;
    }

    /**
     * @param mixed $price_level
     */
    public function setPriceLevel($price_level)
    {
        $this->price_level = $price_level;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getOathSubscriptionId()
    {
        return $this->oath_subscription_id;
    }

    /**
     * @param mixed $oath_subscription_id
     */
    public function setOathSubscriptionId($oath_subscription_id)
    {
        $this->oath_subscription_id = $oath_subscription_id;
    }

    /**
     * @return mixed
     */
    public function getBusinessType()
    {
        return $this->business_type;
    }

    /**
     * @param mixed $business_type
     */
    public function setBusinessType($business_type)
    {
        $this->business_type = $business_type;
    }
}
