<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Payment
 *
 * @ORM\Table(name="shop_payments")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\PaymentRepository")
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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    private $amount;

    /**
     * @var UserOrder
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", inversedBy="payment")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\UserPlugin", mappedBy="payment")
     */
    private $userPlugins;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userPlugins = new ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Payment
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
     * Set order
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $order
     * @return Payment
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
     * Add userPlugins
     *
     * @param \LocalsBest\ShopBundle\Entity\UserPlugin $userPlugins
     * @return Payment
     */
    public function addUserPlugin(UserPlugin $userPlugins)
    {
        $this->userPlugins[] = $userPlugins;

        return $this;
    }

    /**
     * Remove userPlugins
     *
     * @param \LocalsBest\ShopBundle\Entity\UserPlugin $userPlugins
     */
    public function removeUserPlugin(UserPlugin $userPlugins)
    {
        $this->userPlugins->removeElement($userPlugins);
    }

    /**
     * Get userPlugins
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserPlugins()
    {
        return $this->userPlugins;
    }
}
