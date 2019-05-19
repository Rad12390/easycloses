<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LocalsBest\ShopBundle\Entity\UserOrder;

/**
 * ManageOrderCharities
 *
 * @ORM\Table(name="shop_charities_hystory")
 * @ORM\Entity
 */
class ManageOrderCharities
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
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", inversedBy="CharitiesHistory")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $orderId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="businessHystory")
     * @ORM\JoinColumn(name="charity_id", referencedColumnName="id")
     */
    private $charityId;

    /**
     * @var string
     *
     * @ORM\Column(name="charity_name", type="string", length=255)
     */
    private $charityName;


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
     * Set orderId
     *
     * @param integer $orderId
     * @return ManageOrderCharities
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set charityId
     *
     * @param integer $charityId
     * @return integer
     */
    public function setCharityId($charityId)
    {
        $this->charityId = $charityId;

        return $this;
    }

    /**
     * Get charityId
     *
     * @return integer 
     */
    public function getCharityId()
    {
        return $this->charityId;
    }

    /**
     * Set charityName
     *
     * @param string $charityName
     * @return string
     */
    public function setCharityName($charityName)
    {
        $this->charityName = $charityName;

        return $this;
    }

    /**
     * Get charityName
     *
     * @return string 
     */
    public function getCharityName()
    {
        return $this->charityName;
    }
}
