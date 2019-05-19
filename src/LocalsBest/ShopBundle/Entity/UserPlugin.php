<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use LocalsBest\UserBundle\Entity\Plugin;
use LocalsBest\UserBundle\Entity\User;

/**
 * UserPlugin
 *
 * @ORM\Table(name="user_plugins")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\UserPluginRepository")
 */
class UserPlugin
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Plugin
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Plugin", inversedBy="userPlugins")
     * @ORM\JoinColumn(name="plugin_id", referencedColumnName="id")
     */
    private $plugin;

    /**
     * @var int
     *
     * @ORM\Column(name="plugin_uses", type="integer", nullable=true)
     */
    private $uses;

    /**
     * @var int
     *
     * @ORM\Column(name="plugin_uses_limit", type="integer", nullable=true)
     */
    private $usesLimit;

    /**
     * @var Payment
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Payment", inversedBy="userPlugins")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @var \DateTime
     *
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserPlugin
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
     * @return UserPlugin
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
     * Set plugin
     *
     * @param \LocalsBest\UserBundle\Entity\Plugin $plugin
     * @return UserPlugin
     */
    public function setPlugin(\LocalsBest\UserBundle\Entity\Plugin $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return \LocalsBest\UserBundle\Entity\Plugin 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set payment
     *
     * @param \LocalsBest\ShopBundle\Entity\Payment $payment
     * @return UserPlugin
     */
    public function setPayment(\LocalsBest\ShopBundle\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \LocalsBest\ShopBundle\Entity\Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set uses
     *
     * @param integer $uses
     * @return UserPlugin
     */
    public function setUses($uses)
    {
        $this->uses = $uses;

        return $this;
    }

    /**
     * Get uses
     *
     * @return integer 
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Set usesLimit
     *
     * @param integer $usesLimit
     * @return UserPlugin
     */
    public function setUsesLimit($usesLimit)
    {
        $this->usesLimit = $usesLimit;

        return $this;
    }

    /**
     * Get usesLimit
     *
     * @return integer 
     */
    public function getUsesLimit()
    {
        return $this->usesLimit;
    }
}
