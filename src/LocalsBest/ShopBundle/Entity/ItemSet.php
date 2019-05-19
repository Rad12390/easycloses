<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ItemSet
 *
 * @ORM\Table(name="shop_item_sets")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\ItemSetRepository")
 */
class ItemSet
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\Range(min="1")
     */
    private $quantity;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_printable", type="boolean" ,options={"default":1})
     */
    private $isPrintable=true;

    /**
     * @var int
     *
     * @ORM\Column(name="plugin_uses_limit", type="integer", nullable=true)
     */
    private $usesLimit;

    /**
     * One Item have Many Set.
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Item", inversedBy="sets")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * Many Sets belongs to One Package.
     * @var Package
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Package", inversedBy="shopItems")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $package;


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
     * Set quantity
     *
     * @param integer $quantity
     * @return ItemSet
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
     * Set isPrintable
     *
     * @param boolean $isPrintable
     * @return ItemSet
     */
    public function setIsPrintable($isPrintable)
    {
        $this->isPrintable = $isPrintable;

        return $this;
    }

    /**
     * Get isPrintable
     *
     * @return boolean 
     */
    public function getIsPrintable()
    {
        return $this->isPrintable;
    }

    /**
     * Set item
     *
     * @param \LocalsBest\ShopBundle\Entity\Item $item
     * @return ItemSet
     */
    public function setItem(Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \LocalsBest\ShopBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set package
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $package
     * @return ItemSet
     */
    public function setPackage(Package $package = null)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return \LocalsBest\ShopBundle\Entity\Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set usesLimit
     *
     * @param integer $usesLimit
     * @return ItemSet
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
