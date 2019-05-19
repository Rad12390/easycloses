<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LocalsBest\UserBundle\Entity\Business;

/**
 * BusinessSkuSet
 *
 * @ORM\Table(name="business_sku_sets")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\BusinessSkuSetRepository")
 */
class BusinessSkuSet
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
     * One Business have Many Sets.
     * @var Business
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="skuSets")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * One Sku have Many Sets.
     * @var Sku
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Sku", inversedBy="skuSets")
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id")
     */
    private $sku;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_able", type="boolean")
     */
    private $isAble;


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
     * Set isAble
     *
     * @param boolean $isAble
     * @return BusinessSkuSet
     */
    public function setIsAble($isAble)
    {
        $this->isAble = $isAble;

        return $this;
    }

    /**
     * Get isAble
     *
     * @return boolean 
     */
    public function getIsAble()
    {
        return $this->isAble;
    }

    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return BusinessSkuSet
     */
    public function setBusiness(Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \LocalsBest\UserBundle\Entity\Business 
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Set sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return BusinessSkuSet
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
}
