<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="destination")
 */
class Destination
{
   /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", inversedBy="destinations")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", )
     */
    private $order;

    /**
     * @ORM\Column(name="charge_id", type="text")
     */
    private $chargeId;
	
	/**
     * @ORM\Column(name="transfer_group", type="text")
     */
    private $transferGroup;
	
	/**
     * @ORM\Column(name="transfer_id", type="text")
     */
    private $transferId;
	
	/**
     * @ORM\Column(type="text")
     */
    private $destination;
	
	/**
     * @ORM\Column(name="user_type", type="text")
     */
    private $userType;
	
	/**
     * @ORM\Column(type="text")
     */
    private $productid;
	
	/**
     * @ORM\Column(type="text")
     */
    private $productAmount;

    /**
     * @ORM\Column(name="shared_amount", type="decimal", precision=11, scale=2)
     */
    private $sharedAmount = 0;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="destinations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", )
     */
    private $user;

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
     * Set chargeId
     *
     * @param string $chargeId
     * @return Destination
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    /**
     * Get chargeId
     *
     * @return string 
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }

    /**
     * Set transferGroup
     *
     * @param string $transferGroup
     * @return Destination
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
     * Set transferId
     *
     * @param string $transferId
     * @return Destination
     */
    public function setTransferId($transferId)
    {
        $this->transferId = $transferId;

        return $this;
    }

    /**
     * Get transferId
     *
     * @return string 
     */
    public function getTransferId()
    {
        return $this->transferId;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return Destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set userType
     *
     * @param string $userType
     * @return Destination
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userType
     *
     * @return string 
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set productid
     *
     * @param string $productid
     * @return Destination
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
     * Set productAmount
     *
     * @param string $productAmount
     * @return Destination
     */
    public function setProductAmount($productAmount)
    {
        $this->productAmount = $productAmount;

        return $this;
    }

    /**
     * Get productAmount
     *
     * @return string 
     */
    public function getProductAmount()
    {
        return $this->productAmount;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Set sharedAmount
     *
     * @param string $sharedAmount
     * @return Destination
     */
    public function setSharedAmount($sharedAmount)
    {
        $this->sharedAmount = $sharedAmount;

        return $this;
    }

    /**
     * Get sharedAmount
     *
     * @return string 
     */
    public function getSharedAmount()
    {
        return $this->sharedAmount;
    }
}
