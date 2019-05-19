<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use LocalsBest\ShopBundle\Validator\Constraints as ShopAssert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Combo
 *
 * @ORM\Table(name="shop_combos")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\ComboRepository")
 * @ShopAssert\QuantityEnough
 */
class Combo
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
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

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
     * Many Combos have One User.
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="shopItems")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * One Combos have One SKU identificator.
     * @var Sku
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\Sku", mappedBy="combo", cascade={"persist"})
     * @Assert\Valid
     */
    private $sku;

    /**
     * Many Combos have Many SKUs.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Sku", inversedBy="combos")
     */
    private $skus;

    /**
     * One Combo have Many SkuSets.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\ComboSkuSet", mappedBy="combo", cascade={"persist"})
     * @Assert\Valid
     */
    private $skuSets;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;


    public function __construct()
    {
        $this->skuSets = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->sku = null;
        $this->title = '';
        $this->quantity= 0;
        $this->status = null;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Combo
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
     * @return Combo
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
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return Combo
     */
    public function setCreatedBy(\LocalsBest\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return Combo
     */
    public function setSku(Sku $sku = null)
    {
        $this->sku = $sku;

        $sku->setCombo($this);

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
     * Add skuSets
     *
     * @param \LocalsBest\ShopBundle\Entity\ComboSkuSet $skuSets
     * @return Combo
     */
    public function addSkuSet(ComboSkuSet $skuSets)
    {
        $this->skuSets[] = $skuSets;

        $skuSets->setCombo($this);

        return $this;
    }

    /**
     * Remove skuSets
     *
     * @param \LocalsBest\ShopBundle\Entity\ComboSkuSet $skuSets
     */
    public function removeSkuSet(ComboSkuSet $skuSets)
    {
        $this->skuSets->removeElement($skuSets);
    }

    /**
     * Get skuSets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkuSets()
    {
        return $this->skuSets;
    }

    public function setTitle($title=null)
    {
        $result =  '';

        if ($title === null || $title == '') {
            $i = 1;
            foreach ($this->skuSets as $skuSet) {
                $part = '';

                /** @var Sku $sku */
                $sku = $skuSet->getSku();

                if ($sku->getPackage() !== null) {
                    $part = $sku->getPackage()->getTitle();
                } elseif ($skuSet->getSku()->getCombo() !== null) {
                    $part = $sku->getCombo()->getTitle();
                }

                $result .= $part . ($this->skuSets->count() != $i ? ';' : '.');
                $i++;
            }
        } else {
            $result = $title;
        }

        $this->title = $result;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getImages()
    {
        $collection =  new ArrayCollection();

        foreach ($this->skuSets as $skuSet) {
            /** @var Sku $sku */
            $sku = $skuSet->getSku();

            if ($sku->getPackage() !== null) {
                $images = $sku->getPackage()->getImages();
            } elseif ($skuSet->getSku()->getCombo() !== null) {
                $images = $sku->getCombo()->getImages();
            }

            foreach ($images as $image) {
                $collection->add($image);
            }
        }

        return $collection;
    }

    public function getSets()
    {
        $collection =  new ArrayCollection();

        foreach ($this->skuSets as $skuSet) {
            /** @var Sku $sku */
            $sku = $skuSet->getSku();

            if ($sku->getPackage() !== null) {
                $sets = $sku->getPackage()->getSets();
            } elseif ($skuSet->getSku()->getCombo() !== null) {
                $sets = $sku->getCombo()->getSets();
            }

            foreach ($sets as $set) {
                $collection->add($set);
            }
        }

        return $collection;
    }

    public function getShortDescription()
    {
        $result =  '';

        foreach ($this->skuSets as $skuSet) {
            $part = '';

            /** @var Sku $sku */
            $sku = $skuSet->getSku();

            if ($sku->getPackage() !== null) {
                $part = $sku->getPackage()->getShortDescription();
            } elseif ($skuSet->getSku()->getCombo() !== null) {
                $part = $sku->getCombo()->getShortDescription();
            }

            $result .= $part . ( $part != '' ? '; ' : '');
        }

        return $result;
    }

    public function getDescription()
    {
        $result =  '';

        foreach ($this->skuSets as $skuSet) {
            $part = '';

            /** @var Sku $sku */
            $sku = $skuSet->getSku();

            if ($sku->getPackage() !== null) {
                $part = $sku->getPackage()->getDescription();
            } elseif ($skuSet->getSku()->getCombo() !== null) {
                $part = $sku->getCombo()->getDescription();
            }

            $result .= $part . ( $part !== '' ? '; ' : '');
        }

        return $result;
    }

    public function getItems()
    {
        $collection =  new ArrayCollection();

        foreach ($this->skuSets as $skuSet) {
            /** @var Sku $sku */
            $sku = $skuSet->getSku();

            if ($sku->getPackage() !== null) {
                $items = $sku->getPackage()->getItems();
            } elseif ($skuSet->getSku()->getCombo() !== null) {
                $items = $sku->getCombo()->getItems();
            }

            foreach ($items as $item) {
                $collection->add($item);
            }
        }

        return $collection;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Combo
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get text of status
     *
     * @return string
     */
    public function getStatusText()
    {
        $statuses = [
            0 => '-',
            1 => 'Draft',
            2 => 'Approved',
            3 => 'Archived',
            4 => 'Published',
        ];

        return $statuses[$this->status];
    }

    /**
     * @Assert\Callback
     */
    public function skuSetsExists(ExecutionContext $context)
    {
        if ($this->skuSets->count() == 0) {
            $context->addViolation('You have to add SKUs!', [], null);
        }
    }

    /**
     * Add skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     * @return Combo
     */
    public function addSkus(\LocalsBest\ShopBundle\Entity\Sku $skus)
    {
        $this->skus[] = $skus;

        return $this;
    }

    /**
     * Remove skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     */
    public function removeSkus(\LocalsBest\ShopBundle\Entity\Sku $skus)
    {
        $this->skus->removeElement($skus);
    }

    /**
     * Get skus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkus()
    {
        return $this->skus;
    }
}
