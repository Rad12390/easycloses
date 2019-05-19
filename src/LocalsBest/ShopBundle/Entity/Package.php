<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\ShopBundle\Entity\Quotes;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContext;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use LocalsBest\ShopBundle\Validator\Constraints as ShopAssert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Package
 *
 * @ORM\Table(name="shop_packages")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\PackageRepository")
 */
//* @ShopAssert\QuantityEnough
class Package
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    

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
     * @ORM\Column(name="short_description", type="string", length=255)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
   /**
     * @ORM\Column(type="string")
     *
     */
    private $images;
     /**
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="ec_shop", fileNameProperty="images")
     * @var File $file
     */
    protected $file;

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
     * Many Packages have One User.
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="shopItems")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ItemSet", mappedBy="package", cascade={"persist"})
     * @Assert\Valid()
     */
    private $sets;

    /**
     * One Package have One SKU identificator.
     * @var Sku
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\Sku", mappedBy="package", cascade={"persist"})
     * @Assert\Valid
     */
    private $sku;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $productmodetype;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $stripeinterval;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
   // private $intervalcount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $stripeplanid;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $stripeproductid;

    /**
     * One Package have Many Restriction.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Restriction", mappedBy="package")
     */
    private $restrictions;

    /**
     * One Package have Many PackageOption.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\PackageOption", mappedBy="package", cascade={"persist"} )
     */
    private $options;

    /**
     * @var IndustryType
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\IndustryType", inversedBy="packages")
     * @ORM\JoinColumn(name="industry_id", referencedColumnName="id")
     */
    private $industryType;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $imagename;
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Quotes", mappedBy="packageId")
     */
    protected $quotedPackage;
    
    /**
     * Many Packages have One User.
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="packageAssignne")
     * @ORM\JoinColumn(name="assignee", referencedColumnName="id")
     */
    private $assignee;
    
    /**
     * @var vendorchoice
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\VendorChoice", mappedBy="packageId")
     */
    protected $vendorchoice;
    
    public function __construct()
    {
        $this->sets = new ArrayCollection();
        $this->restrictions = new ArrayCollection();
        $this->options = new ArrayCollection();
        
    }

    public function __clone()
    {
        $this->id = null;
        $this->title = '';
        $this->quantity= 0;
        $this->status = null;
    }


    public function __toString()
    {
        return $this->title;
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
     * Set title
     *
     * @param string $title
     * @return Package
     */
    public function setImagename($imagename)
    {
        $this->imagename = $imagename;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getImagename()
    {
        return $this->imagename;
    }
    /**
     * Set title
     *
     * @param string $title
     * @return Package
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Package
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Package
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        $result = '';

        if (empty($this->description) or is_null($this->description)) {
            foreach ($this->getPrintableItems() as $item) {
                $result .= $item->getDescription() . ';';
            }
        } else {
            $result = $this->description;
        }

        return $result;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Package
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
     * Get text of status
     *
     * @return string
     */
    public function getStatusText()
    {
        $statuses = [
            1 => 'Draft',
            2 => 'Published',
            3 => 'Archived',
            4 => 'Pending for approval',
            5 => 'Disapproved',
        ];

       return $statuses[$this->status];
       
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Package
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
     * @return Package
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
     * @return Package
     */
    public function setCreatedBy(User $createdBy = null)
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
     * Add items
     *
     * @param \LocalsBest\ShopBundle\Entity\ItemSet $set
     * @return Package
     */
    public function addSet(ItemSet $set)
    {
        $this->sets[] = $set;

        $set->setPackage($this);

        return $this;
    }

    /**
     * Remove sets
     *
     * @param \LocalsBest\ShopBundle\Entity\ItemSet $set
     */
    public function removeSet(ItemSet $set)
    {
        $this->sets->removeElement($set);
    }

    /**
     * Get items
     *
     * @return ArrayCollection
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Package
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
     * Set sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return Package
     */
    public function setSku(Sku $sku = null)
    {
        $this->sku = $sku;

        $sku->setPackage($this);

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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Package
     */
    public function setFile(File $image = null)
    {
        $this->file = $image;

        return $this;
    }
    
    
//    public function setFile(File $image = null)
//    {
//        $this->file = $image;
//       if ($this->file instanceof UploadedFile) {
//            $this->updatedAt = new \DateTime('now');
//        }
//        
//       
//        return $this;
//    }

    /**
     * @return File|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Add restrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $restrictions
     * @return Package
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
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Add options
     *
     * @param \LocalsBest\ShopBundle\Entity\PackageOption $options
     * @return Package
     */
    public function addOption(PackageOption $options)
    {
        $this->options[] = $options;

        $options->setPackage($this);

        return $this;
    }

    /**
     * Remove options
     *
     * @param \LocalsBest\ShopBundle\Entity\PackageOption $options
     */
    public function removeOption(PackageOption $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }

//    public function getImages()
//    {
//        $collection = new ArrayCollection();
//        foreach ($this->getSets() as $set) {
//            foreach ($set->getItem()->getImages() as $image) {
//                $collection->add($image);
//            }
//        }
//
//        return $collection;
//    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }
    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        $collection = new ArrayCollection();

        foreach ($this->getSets() as $set) {
            $collection->add($set->getItem());
        }

        return $collection;
    }

    public function getPrintableItems()
    {
        $collection = new ArrayCollection();

        foreach ($this->getSets() as $set) {
            if ($set->getIsPrintable() == true) {
            if($set->getItem()){
                $collection->add($set->getItem());
            }
            }
        }
       
        return $collection;
    }

    /**
     * @Assert\Callback
     */
    public function setsExists(ExecutionContext $context)
    {
        if ($this->sets->count() == 0) {
            $context->addViolation('You have to add Items to Package!', [], null);
        }
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Set industryType
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industryType
     * @return Package
     */
    public function setIndustryType(IndustryType $industryType = null)
    {
        $this->industryType = $industryType;

        return $this;
    }

    /**
     * Get industryType
     *
     * @return \LocalsBest\UserBundle\Entity\IndustryType 
     */
    public function getIndustryType()
    {
        return $this->industryType;
    }

    public function getIndustryPath()
    {
        $item = $this->getItems()->first();

        $path = $item->getExternalLink();

        return $path;
    }

    /**
     * Set stripeplanid
     *
     * @param string $stripeplanid
     * @return Package
     */
    public function setStripeplanid($stripeplanid)
    {
        $this->stripeplanid = $stripeplanid;

        return $this;
    }

    /**
     * Get stripeplanid
     *
     * @return string 
     */
    public function getStripeplanid()
    {
        return $this->stripeplanid;
    }

    /**
     * Set stripeproductid
     *
     * @param string $stripeproductid
     * @return Package
     */
    public function setStripeproductid($stripeproductid)
    {
        $this->stripeproductid = $stripeproductid;

        return $this;
    }

    /**
     * Get stripeproductid
     *
     * @return string 
     */
    public function getStripeproductid()
    {
        return $this->stripeproductid;
    }

    /**
     * Set productmodetype
     *
     * @param string $productmodetype
     * @return Package
     */
    public function setProductmodetype($productmodetype)
    {
        $this->productmodetype = $productmodetype;

        return $this;
    }

    /**
     * Get productmodetype
     *
     * @return string 
     */
    public function getProductmodetype()
    {
        return $this->productmodetype;
    }

    /**
     * Set stripeinterval
     *
     * @param string $stripeinterval
     * @return Package
     */
    public function setStripeinterval($stripeinterval)
    {
        $this->stripeinterval = $stripeinterval;

        return $this;
    }

    /**
     * Get stripeinterval
     *
     * @return string 
     */
    public function getStripeinterval()
    {
        return $this->stripeinterval;
    }

    /**
     * Set intervalcount
     *
     * @param string $intervalcount
     * @return Package
     */
//    public function setIntervalcount($intervalcount)
//    {
//        $this->intervalcount = $intervalcount;
//
//        return $this;
//    }

    /**
     * Get intervalcount
     *
     * @return string 
     */
  //  public function getIntervalcount()
   // {
   //     return $this->intervalcount;
   // }
   
    public function setQuotedPackage($quotedPackage)
    {
        $this->quotedPackage = $quotedPackage;

        return $this;
    }

 
    public function getQuotedPackage()
    {
        return $this->quotedPackage;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $assignee
     * @return Package
     */
    public function setAssignne($assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getAssignne()
    {
        return $this->assignee;
    }
    
    public function setVendorChoice($vendorchoice)
    {
        $this->vendorchoice = $vendorchoice;

        return $this;
    }

    /**
     * Get vendorchoice
     *
     * @return \LocalsBest\ShopBundle\Entity\VendorChoice
     */
    public function getVendorChoice()
    {
        return $this->vendorchoice;
    }
}
