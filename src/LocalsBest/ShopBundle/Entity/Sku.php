<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LocalsBest\UserBundle\Entity\Business;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Sku
 *
 * @ORM\Table(name="shop_skus")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\SkuRepository")
 */
class Sku
{
    const STATUS_DRAFT = 1;
    const STATUS_APPROVED = 2;
    const STATUS_ARCHIVED = 3;

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
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_since", type="datetime", nullable=true)
     */
    private $activeSince;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_till", type="datetime", nullable=true)
     */
    private $activeTill;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", options={"default"=0})
     */
    private $views = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_for_sale", type="boolean")
     */
    private $isForSale = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="archived_date", type="datetime", nullable=true)
     */
    private $archivedDate;

    /**
     * One Package have One SKU identificator.
     * @var Package
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\Package", inversedBy="sku")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $package;

    /**
     * One Combo have One SKU identificator.
     * @var Combo
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\Combo", inversedBy="sku")
     * @ORM\JoinColumn(name="combo_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $combo;

    /**
     * Many Combos have Many SKUs.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Combo", mappedBy="skus")
     */
    private $combos;

    /**
     * Many Categories have Many SKUs.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Category", inversedBy="skus")
     * @ORM\JoinTable(name="categories_skus")
     */
    private $categories;

    /**
     * One Sku have Many Restriction.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Restriction", mappedBy="skus")
     */
    private $restrictions;

    /**
     * One Sku have Many Restriction.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Restriction", mappedBy="adminSkus", cascade={"persist", "remove"})
     */
    private $adminRestrictions;

    /**
     * One Sku have Many ComboSkuSets.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\ComboSkuSet", mappedBy="sku")
     */
    private $comboSets;

    /**
     * One Sku have Many Prices.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Price", mappedBy="sku", cascade={"persist", "remove"})
     * @Assert\NotBlank
     * @Assert\Valid
     */
    private $prices;

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Tag", inversedBy="skus")
     * @ORM\JoinTable(name="shop_skus_tags")
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="skuDisable")
     * @ORM\JoinTable(name="disable_skus_businesses")
     */
    private $disableForBusinesses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\OrderItem", mappedBy="sku")
     */
    private $shopItems;


    public function __construct()
    {
        $this->status = self::STATUS_DRAFT;

        $this->combos = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->restrictions = new ArrayCollection();
        $this->adminRestrictions = new ArrayCollection();
        $this->comboSets = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->disableForBusinesses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();

//        if ($this->getPackage() !== null) {
//            $package = $this->getPackage();
//            return $package->getTitle() . ' ( able: ' . $package->getQuantity() . ')';
//        }
//
//        $result =  '';
//        foreach ($this->getCombo()->getSkuSets() as $skuSet) {
//            $part = '';
//
//            /** @var Sku $sku */
//            $sku = $skuSet->getSku();
//
//            if ($sku->getPackage() !== null) {
//                $part = $sku->getPackage()->getTitle();
//            } elseif ($skuSet->getSku()->getCombo() !== null) {
//                $part = $sku->getCombo()->getTitle();
//            }
//
//            $result .= $part . ( $part !== '' ? '; ' : '');
//        }
//
//        return $result;
    }

    public function getImages()
    {
        return $this->getProductEntity()->getImages();
    }

    public function getTitle()
    {
        return $this->getProductEntity()->getTitle();
    }

    public function getShortDescription()
    {
        return $this->getProductEntity()->getShortDescription();
    }

    public function getDescription()
    {
        return $this->getProductEntity()->getDescription();
    }

    public function getPrice()
    {
        return $this->getPrices()->first();
    }

    public function isLinkTo()
    {
        return ($this->getProductEntity()->getItems()->first()->getType() == 3);
    }

    public function getItems()
    {
        return $this->getProductEntity()->getItems();
    }

    public function getComments()
    {
        $collection = new ArrayCollection();

        foreach ($this->getPrintableItems() as $item) {
            foreach ($item->getComments() as $comment) {
                $collection->add($comment);
            }
        }

        return $collection;
    }

    public function getItemsSets()
    {
        return $this->getProductEntity()->getSets();
    }

    public function getPrintableItems()
    {
        $collection = new ArrayCollection();

        foreach ($this->getProductEntity()->getSets() as $set) {
            if ($set->getIsPrintable() == true) {
                $collection->add($set->getItem());
            }
        }

        return $collection;
    }

    public function getCreatedAt()
    {
        return $this->getProductEntity()->getCreatedAt();
    }

    public function getCreatedBy()
    {
        return $this->getProductEntity()->getCreatedBy();
    }

    public function getStatusText()
    {
        return $this->getProductEntity()->getStatusText();
    }

    public function getAverageRating()
    {
        $ratingSum = 0;
        $count = 0;

        foreach ($this->getComments() as $comment) {
            $ratingSum += $comment->getRating();
            $count++;
        }

        return $count == 0 ? 0 : number_format($ratingSum/$count, 1);
    }

    public function getReviewsCount()
    {
        return count($this->getComments());
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
     * Set status
     *
     * @param string $status
     * @return Sku
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
     * Set isForSale
     *
     * @param boolean $isForSale
     * @return Sku
     */
    public function setIsForSale($isForSale)
    {
        $this->isForSale = $isForSale;

        return $this;
    }

    /**
     * Get isForSale
     *
     * @return boolean
     */
    public function getIsForSale()
    {
        return $this->isForSale;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return Sku
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set archivedDate
     *
     * @param \DateTime $archivedDate
     * @return Sku
     */
    public function setArchivedDate($archivedDate)
    {
        $this->archivedDate = $archivedDate;

        return $this;
    }

    /**
     * Get archivedDate
     *
     * @return \DateTime
     */
    public function getArchivedDate()
    {
        return $this->archivedDate;
    }

    /**
     * Set package
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $package
     * @return Sku
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
     * Set combo
     *
     * @param \LocalsBest\ShopBundle\Entity\Combo $combo
     * @return Sku
     */
    public function setCombo(Combo $combo = null)
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
     * Add combos
     *
     * @param \LocalsBest\ShopBundle\Entity\Combo $combos
     * @return Sku
     */
    public function addCombo(Combo $combos)
    {
        $this->combos[] = $combos;

        return $this;
    }

    /**
     * Remove combos
     *
     * @param \LocalsBest\ShopBundle\Entity\Combo $combos
     */
    public function removeCombo(Combo $combos)
    {
        $this->combos->removeElement($combos);
    }

    /**
     * Get combos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCombos()
    {
        return $this->combos;
    }

    /**
     * Add categories
     *
     * @param \LocalsBest\ShopBundle\Entity\Category $categories
     * @return Sku
     */
    public function addCategory(Category $categories)
    {
        $this->categories[] = $categories;

        $categories->addSkus($this);

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \LocalsBest\ShopBundle\Entity\Category $categories
     */
    public function removeCategory(Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set activeSince
     *
     * @param \DateTime $activeSince
     * @return Sku
     */
    public function setActiveSince($activeSince)
    {
        $this->activeSince = $activeSince;

        return $this;
    }

    /**
     * Get activeSince
     *
     * @return \DateTime
     */
    public function getActiveSince()
    {
        return $this->activeSince;
    }

    /**
     * Set activeTill
     *
     * @param \DateTime $activeTill
     * @return Sku
     */
    public function setActiveTill($activeTill)
    {
        $this->activeTill = $activeTill;

        return $this;
    }

    /**
     * Get activeTill
     *
     * @return \DateTime
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }


    /**
     * Add restrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $restrictions
     * @return Sku
     */
    public function addRestriction(Restriction $restrictions)
    {
        $this->restrictions[] = $restrictions;

        return $this;
    }

    /**
     * Remove restrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $restrictions
     */
    public function removeRestriction(Restriction $restrictions)
    {
        $this->restrictions->removeElement($restrictions);
    }

    /**
     * Get restrictions
     *
     * @return ArrayCollection
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Add comboSets
     *
     * @param \LocalsBest\ShopBundle\Entity\ComboSkuSet $comboSets
     * @return Sku
     */
    public function addComboSet(ComboSkuSet $comboSets)
    {
        $this->comboSets[] = $comboSets;

        return $this;
    }

    /**
     * Remove comboSets
     *
     * @param \LocalsBest\ShopBundle\Entity\ComboSkuSet $comboSets
     */
    public function removeComboSet(ComboSkuSet $comboSets)
    {
        $this->comboSets->removeElement($comboSets);
    }

    /**
     * Get comboSets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComboSets()
    {
        return $this->comboSets;
    }

    /**
     * Add prices
     *
     * @param \LocalsBest\ShopBundle\Entity\Price $prices
     * @return Sku
     */
    public function addPrice(Price $prices)
    {
        $this->prices[] = $prices;

        $prices->setSku($this);

        return $this;
    }

    /**
     * Remove prices
     *
     * @param \LocalsBest\ShopBundle\Entity\Price $prices
     */
    public function removePrice(Price $prices)
    {
        $this->prices->removeElement($prices);
    }

    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @return Combo|Package
     */
    public function getProductEntity()
    {
        if ($this->getPackage() !== null) {
            $productEntity = $this->getPackage();
        } else {
            $productEntity = $this->getCombo();
        }

        return $productEntity;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return Sku
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add tags
     *
     * @param \LocalsBest\ShopBundle\Entity\Tag $tags
     * @return Sku
     */
    public function addTag(Tag $tags)
    {
        $this->tags[] = $tags;

        $tags->addSku($this);

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \LocalsBest\ShopBundle\Entity\Tag $tags
     */
    public function removeTag(Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function getMarkup()
    {
        $items = $this->getItems();

        return $items->first()->getMarkup();
    }

    public function getOptions()
    {
        if ($this->getPackage() !== null) {
            return $this->getPackage()->getOptions();
        }

        return [];
    }

    /**
     * @Assert\Callback
     */
    public function pricesExists(ExecutionContext $context)
    {
        if (
            $this->prices->count() == 0
            && $this->getProductEntity()->getType() == 1
        ) {
            $context->addViolation('You have to add Prices!', [], null);
        }
    }

    public function getRetailPrice()
    {
	//old code for market price
//        if ($this->getPrice() !== false) {
//            return (1 + $this->getMarkup() / 100) * $this->getPrice()->getAmount();
//        } else {
//            return 0;
//        }
	return $this->getPrice()->getretailPrice();
    }

    /**
     * Add disableForBusiness
     *
     * @param \LocalsBest\UserBundle\Entity\Business $disableForBusiness
     * @return Sku
     */
    public function addDisableForBusiness(Business $disableForBusiness)
    {
        $this->disableForBusinesses[] = $disableForBusiness;

        return $this;
    }

    /**
     * Remove disableForBusiness
     *
     * @param \LocalsBest\UserBundle\Entity\Business $disableForBusiness
     */
    public function removeDisableForBusiness(Business $disableForBusiness)
    {
        $this->disableForBusinesses->removeElement($disableForBusiness);
    }

    /**
     * Get disableForBusiness
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDisableForBusiness()
    {
        return $this->disableForBusinesses;
    }

    public function getItemsRestrictions()
    {
        $items = $this->getItems();

        $restrictions= [];

        foreach ($items as $item) {
            foreach ($item->getRestrictions() as $restriction) {
                $restrictions[$restriction->getType()] = true;
            }
        }

        return $restrictions;
    }

    /**
     * Get disableForBusinesses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDisableForBusinesses()
    {
        return $this->disableForBusinesses;
    }

    /**
     * @return ArrayCollection
     */
    public function getShopItems()
    {
        return $this->shopItems;
    }

    /**
     * @param ArrayCollection $shopItems
     */
    public function setShopItems($shopItems)
    {
        $this->shopItems = $shopItems;
    }

    /**
     * Add shopItems
     *
     * @param \LocalsBest\ShopBundle\Entity\OrderItem $shopItems
     * @return Sku
     */
    public function addShopItem(OrderItem $shopItems)
    {
        $this->shopItems[] = $shopItems;

        return $this;
    }

    /**
     * Remove shopItems
     *
     * @param \LocalsBest\ShopBundle\Entity\OrderItem $shopItems
     */
    public function removeShopItem(OrderItem $shopItems)
    {
        $this->shopItems->removeElement($shopItems);
    }

    /**
     * Add adminRestrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $adminRestrictions
     * @return Sku
     */
    public function addAdminRestriction(Restriction $adminRestrictions)
    {
        $this->adminRestrictions[] = $adminRestrictions;

        $adminRestrictions->addAdminSkus($this);

        return $this;
    }

    /**
     * Remove adminRestrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $adminRestrictions
     */
    public function removeAdminRestriction(Restriction $adminRestrictions)
    {
        $this->adminRestrictions->removeElement($adminRestrictions);
    }

    /**
     * Get adminRestrictions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdminRestrictions()
    {
        return $this->adminRestrictions;
    }
}
