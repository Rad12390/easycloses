<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ComboSkuSet
 *
 * @ORM\Table(name="combo_sku_sets")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\ComboSkuSetRepository")
 */
class ComboSkuSet
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
     * Many Sets have One Combo.
     * @var Combo
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Combo", inversedBy="skuSets")
     * @ORM\JoinColumn(name="combo_id", referencedColumnName="id" , onDelete="CASCADE")
     */
    private $combo;

    /**
     * Many Sets have One Sku.
     * @var Sku
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Sku", inversedBy="comboSets")
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id")
     */
    private $sku;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


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
     * @return ComboSkuSet
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ComboSkuSet
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ComboSkuSet
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set combo
     *
     * @param \LocalsBest\ShopBundle\Entity\Combo $combo
     * @return ComboSkuSet
     */
    public function setCombo(\LocalsBest\ShopBundle\Entity\Combo $combo = null)
    {
        $this->combo = $combo;

        return $this;
    }

    /**
     * Get combo
     *
     * @return \LocalsBest\ShopBundle\Entity\Combo 
     */
    public function getCombo()
    {
        return $this->combo;
    }

    /**
     * Set sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return ComboSkuSet
     */
    public function setSku(\LocalsBest\ShopBundle\Entity\Sku $sku = null)
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
