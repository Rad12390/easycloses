<?php


namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\UserOrder;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Gedmo\Mapping\Annotation as Gedmo;
use LocalsBest\ShopBundle\Entity\ManageOrderCharities;

/**
 * LocalsBest\UserBundle\Entity\Business
 *
 * @ORM\Table(name="businesses")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\BusinessRepository")
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 * @ORM\HasLifecycleCallbacks()
 */
class Business
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="ownedBusiness")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 40,
     *      minMessage = "Your business name must be at least {{ limit }} characters long",
     *      maxMessage = "Your business name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=100, name="name", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100, name="themeColor")
     */
    protected $themeColor = 'light';

    /**
     * @Gedmo\Slug(fields={"name", "id"}, updatable=true)
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $rating = 1;

    /**
     * @ORM\Column(type="string", length=100, name="contact_name", nullable=true)
     * @Assert\NotBlank()
     */
    protected $contactName;

    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Address", cascade={"all"})
     * @Assert\Valid
     */
    protected $address;

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\IndustryType", inversedBy="businesses")
     * @ORM\JoinTable(name="business_type")
     * @Assert\NotBlank()
     */
    protected $types;

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\State")
     * @Assert\NotNull()
     */
    protected $workingStates;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\AssociationRow", mappedBy="business")
     */
    protected $associationRows;

    /**
     * @Vich\UploadableField(mapping="users", fileNameProperty="fileName")
     *
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File $file
     * @Assert\Image
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, name="file_name", nullable=true )
     *
     */
    protected $fileName;

    /**
     * Business staff
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="businesses", cascade={"persist"})
     */
    protected $staffs;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\AboutMe", mappedBy="business", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $aboutMe;

    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Contact", inversedBy="business", cascade={"all"})
     *
     */
    protected $contact;

    /**
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="integer", name="bio_clicks", options={"default" = 0})
     */
    protected $bio_clicks = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\BusinessView", mappedBy="business")
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    protected $bio_views;

    /**
     * @ORM\Column(type="text", name="form", nullable=true, options={"default" = null})
     */
    protected $business_form = null;

    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Plan", inversedBy="business")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id")
     */
    protected $plan;

    /**
     * @ORM\Column(type="boolean", name="search_hidden", nullable=true, options={"default" = false})
     */
    protected $searchHidden = false;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="forBusiness")
     */
    private $productAble;

    /**
     * @ORM\Column(type="boolean", name="is_claimed", options={"default" = true})
     */
    private $isClaimed = true;

    /**
     * @ORM\Column(type="string", name="shop_sku_status", options={"default" = "can_be_sold"})
     */
    private $shopSkuStatus = 'can_be_sold';

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Sku", mappedBy="disableForBusinesses")
     */
    private $skuDisable;

    

    /**
     * @var int
     *
     * @ORM\Column(name="shop_percentage", type="integer", nullable=true)
     */
    private $shopPercentage;

    /**
     * @ORM\Column(type="datetime", options={"default" = "2017-10-05 19:00:00"})
     * @Gedmo\Timestampable(on="create")
     * @var integer
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", options={"default" = "2017-10-05 19:00:00"})
     * @Gedmo\Timestampable(on="update")
     * @var integer
     */
    protected $updated_at;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", mappedBy="business")
     */
    private $shopOrders;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\ManageOrderCharities", mappedBy="charityId")
     */
    private $businessHystory;
    
    /**
     * @ORM\Column(name="residualAmount", type="integer")
     */
    private $residualAmount = 0;
    
    /**
     * @ORM\Column(type="boolean", name="residualStatus", options={"default" = true})
     */
    private $residualStatus = true;
    
    
    

    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->contact->setBusiness($this);
//        $this->aboutMe->add(new AboutMe('No Bio entered yet'));
        $this->contact          = new Contact();
        $this->aboutMe          = new ArrayCollection();
        $this->staffs           = new ArrayCollection();
        $this->workingStates    = new ArrayCollection();
        $this->associationRows  = new ArrayCollection();
        $this->address          = new Address();
        $this->types            = new ArrayCollection();
        $this->productAble      = new ArrayCollection();
        $this->bio_views        = new ArrayCollection();
        $this->skuDisable       = new ArrayCollection();
        $this->shopOrders       = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     * @return Business
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Business
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return Business
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set owner
     *
     * @param \LocalsBest\UserBundle\Entity\User $owner
     * @return Business
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add address
     *
     * @param \LocalsBest\UserBundle\Entity\Address $address
     * @return Business
     */
    public function addAddress(Address $address)
    {
        $this->address[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \LocalsBest\UserBundle\Entity\Address $address
     */
    public function removeAddress(Address $address)
    {
        $this->address->removeElement($address);
    }

    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Business
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return Business
     */
    public function setFile($file = null)
    {
        if ($file instanceof \Symfony\Component\HttpFoundation\File\File) {
            $this->file = $file;

//            $this->fileName = $file->getFilename();
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set address
     *
     * @param \LocalsBest\UserBundle\Entity\Address $address
     * @return Business
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Add staffs
     *
     * @param \LocalsBest\UserBundle\Entity\User $staffs
     * @return Business
     */
    public function addStaff(User $staffs)
    {
        $this->staffs[] = $staffs;

        return $this;
    }

    /**
     * Remove staffs
     *
     * @param \LocalsBest\UserBundle\Entity\User $staffs
     */
    public function removeStaff(User $staffs)
    {
        $this->staffs->removeElement($staffs);
    }

    /**
     * Get staffs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStaffs()
    {
        return $this->staffs;
    }

    /**
     * Add aboutMe
     *
     * @param \LocalsBest\UserBundle\Entity\AboutMe $aboutMe
     * @return Business
     */
    public function addAboutMe(AboutMe $aboutMe)
    {
        $this->aboutMe[] = $aboutMe;

        return $this;
    }

    /**
     * Remove aboutMe
     *
     * @param \LocalsBest\UserBundle\Entity\AboutMe $aboutMe
     */
    public function removeAboutMe(AboutMe $aboutMe)
    {
        $this->aboutMe->removeElement($aboutMe);
    }

    /**
     * Get aboutMe
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Business
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set contact
     *
     * @param \LocalsBest\UserBundle\Entity\Contact $contact
     * @return Business
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \LocalsBest\UserBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return mixed
     */
    public function getThemeColor()
    {
        return $this->themeColor;
    }

    /**
     * @param mixed $themeColor
     */
    public function setThemeColor($themeColor)
    {
        $this->themeColor = $themeColor;
    }

    /**
     * Add workingStates
     *
     * @param \LocalsBest\UserBundle\Entity\State $workingStates
     * @return Business
     */
    public function addWorkingState(State $workingStates)
    {
        $this->workingStates[] = $workingStates;

        return $this;
    }

    /**
     * Remove workingStates
     *
     * @param \LocalsBest\UserBundle\Entity\State $workingStates
     */
    public function removeWorkingState(State $workingStates)
    {
        $this->workingStates->removeElement($workingStates);
    }

    /**
     * Get working states
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkingStates()
    {
        return $this->workingStates;
    }

    public function getWorkingStatesIds()
    {
        $result = [];

        foreach ($this->workingStates as $workingState) {
            $result[] = $workingState->getId();
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getAssociationRows()
    {
        return $this->associationRows;
    }

    /**
     * @param mixed $associationRows
     */
    public function setAssociationRows($associationRows)
    {
        $this->associationRows = $associationRows;
    }

    /**
     * Add associationRows
     *
     * @param \LocalsBest\UserBundle\Entity\AssociationRow $associationRows
     * @return Business
     */
    public function addAssociationRow(AssociationRow $associationRows)
    {
        $this->associationRows[] = $associationRows;

        return $this;
    }

    /**
     * Remove associationRows
     *
     * @param \LocalsBest\UserBundle\Entity\AssociationRow $associationRows
     */
    public function removeAssociationRow(AssociationRow $associationRows)
    {
        $this->associationRows->removeElement($associationRows);
    }

    /**
     * @return mixed
     */
    public function getBioClicks()
    {
        return $this->bio_clicks;
    }

    /**
     * @param mixed $bio_clicks
     */
    public function setBioClicks($bio_clicks)
    {
        $this->bio_clicks = $bio_clicks;
    }

    /**
     * @return mixed
     */
    public function getBusinessForm()
    {
        return $this->business_form;
    }

    /**
     * @param mixed $business_form
     */
    public function setBusinessForm($business_form)
    {
        $this->business_form = $business_form;
    }

    /**
     * @return mixed
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @param mixed $plan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
    }

    /**
     * Add types
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $types
     * @return Business
     */
    public function addType(IndustryType $types)
    {
        $this->types[] = $types;

        return $this;
    }

    /**
     * Remove types
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $types
     */
    public function removeType(IndustryType $types)
    {
        $this->types->removeElement($types);
    }

    /**
     * Get types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypes()
    {
        return $this->types;
    }

    public function getTypesIds()
    {
        $result = [];
        foreach ($this->types as $item) {
            $result[] = $item->getId();
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getSearchHidden()
    {
        return $this->searchHidden;
    }

    /**
     * @param mixed $searchHidden
     */
    public function setSearchHidden($searchHidden)
    {
        $this->searchHidden = $searchHidden;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return Business
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Add productAble
     *
     * @param \LocalsBest\UserBundle\Entity\Product $productAble
     * @return Business
     */
    public function addProductAble(Product $productAble)
    {
        $this->productAble[] = $productAble;

        return $this;
    }

    /**
     * Remove productAble
     *
     * @param \LocalsBest\UserBundle\Entity\Product $productAble
     */
    public function removeProductAble(Product $productAble)
    {
        $this->productAble->removeElement($productAble);
    }

    /**
     * Get productAble
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductAble()
    {
        return $this->productAble;
    }

    /**
     * @return ArrayCollection
     */
    public function getBioViews()
    {
        return $this->bio_views;
    }

    /**
     * @param ArrayCollection $bio_views
     */
    public function setBioViews($bio_views)
    {
        $this->bio_views = $bio_views;
    }

    /**
     * Set isClaimed
     *
     * @param boolean $isClaimed
     * @return Business
     */
    public function setIsClaimed($isClaimed)
    {
        $this->isClaimed = $isClaimed;

        return $this;
    }

    /**
     * Get isClaimed
     *
     * @return boolean
     */
    public function getIsClaimed()
    {
        return $this->isClaimed;
    }

    /**
     * Set created
     *
     * @param integer $createdAt
     * @return Business
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated
     *
     * @param string $updatedAt
     * @return Business
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return mixed
     */
    public function getShopSkuStatus()
    {
        return $this->shopSkuStatus;
    }

    /**
     * @param mixed $shopSkuStatus
     */
    public function setShopSkuStatus($shopSkuStatus)
    {
        $this->shopSkuStatus = $shopSkuStatus;
    }

    /**
     * Add skuDisable
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skuDisable
     * @return Business
     */
    public function addSkuDisable(Sku $skuDisable)
    {
        $this->skuDisable[] = $skuDisable;
        return $this;
    }

    /**
     * Remove skuDisable
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skuDisable
     */
    public function removeSkuDisable(Sku $skuDisable)
    {
        $this->skuDisable->removeElement($skuDisable);
    }

    /**
     * Get skuDisable
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkuDisable()
    {
        return $this->skuDisable;
    }

    public function isDisabledSku(Sku $product)
    {
        if ($this->getSkuDisable()->contains($product)) {
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getShopPercentage()
    {
        return $this->shopPercentage;
    }

    /**
     * @param int $shopPercentage
     */
    public function setShopPercentage($shopPercentage)
    {
        $this->shopPercentage = $shopPercentage;
    }

    /**
     * Add shop order
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $shopOrder
     *
     * @return Business
     */
    public function addShopOrder(UserOrder $shopOrder)
    {
        $this->shopOrders[] = $shopOrder;

        return $this;
    }

    /**
     * Remove shop order
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $shopOrder
     */
    public function removeShopOrder(UserOrder $shopOrder)
    {
        $this->shopOrders->removeElement($shopOrder);
    }

    /**
     * Get shop orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShopOrders()
    {
        return $this->shopOrders;
    }
    
    public function getBusinessHystory()
    {
        return $this->businessHystory;
    }
    /**
     * Set splitHystory
     *
     * @param \LocalsBest\ShopBundle\Entity\ManageOrderCharities $manageOrderCharities
     * @return UserOrder
     */
    public function setBusinessHystory(ManageOrderCharities $manageOrderCharities = null)
    {
        $this->businessHystory = $manageOrderCharities;

        return $this;
    }
    /**
     * Set ResidualAmount
     *
     * @return \integer
     */
    public function setResidualAmount()
    {
        $this->residualAmount = $residualAmount;

        return $this;
    }

    /**
     * Get ResidualAmount
     *
     * @return \integer
     */
    public function getResidualAmount()
    {
        return $this->residualAmount;
    }
    /**
     * Set residualStatus
     *
     * @return \boolean
     */
    public function setResidualStatus()
    {
        $this->residualStatus = $residualStatus;

        return $this;
    }

    /**
     * Get residualStatus
     *
     * @return \boolean
     */
    public function getResidualStatus()
    {
        return $this->residualStatus;
    }
    
    
}