<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ProductRepository")
 */
class Product
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
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=127)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="short_description", type="text")
     */
    private $short_description;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="addon_part", type="string", nullable=true)
     */
    private $addon_part;

    /**
     * @var string
     *
     * @ORM\Column(name="addon_type", type="string", nullable=true)
     * @Assert\Expression(
     *     "not(this.getAddonPart() != null and value == null)",
     *     message="Addon Type is required if You set Addon Part for Product"
     * )
     */
    private $addon_type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_from", type="datetime", options={"default" : "2018-01-01 00:00:00"})
     */
    protected $activeFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_to", type="datetime", options={"default" : "2018-12-31 23:59:59"})
     */
    protected $activeTo;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", options={"default" : 0})
     */
    private $views = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_system", type="boolean", options={"default" : false})
     */
    private $is_system = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="created_products")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $created_by;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\ProductType", mappedBy="product")
     * @ORM\OrderBy({"price" = "ASC"})
     */
    private $types;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="prepayment", type="boolean")
     */
    protected $prepayment = false   ;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status = true;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="products")
     * @ORM\JoinTable(name="users_products")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="product", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $feedbacks;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="products")
     * @ORM\JoinTable(name="products_tags")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="ProductImage", mappedBy="product", cascade={"persist"})
     * @ORM\OrderBy({"orderNumber" = "ASC"})
     * @Assert\Valid()
     */
    private $images;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Business", inversedBy="productAble")
     * @ORM\JoinTable(name="product_business_advertisement")
     */
    private $forBusiness;

    /**
     * @var array
     *
     * @ORM\Column(name="pages", type="array", nullable=true)
     */
    private  $forPage;

    /**
     * @var array
     *
     * @ORM\Column(name="statuses", type="array", nullable=true)
     */
    private $forStatus;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    private $forRoles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="State", inversedBy="productAble")
     * @ORM\JoinTable(name="product_state_advertisement")
     */
    private $forState;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="IndustryType", inversedBy="productAble")
     * @ORM\JoinTable(name="product_industry_advertisement")
     */
    private $forIndustry;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_able_for_slider", type="boolean", options={"default" : false})
     */
    private $is_able_for_slider = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_able_for_shop", type="boolean", options={"default" : true})
     */
    private $is_able_for_shop = true;

    
    /**
     * @var string
     *
     * @ORM\Column(name="link_to", type="string", nullable=true)
     */
    private $linkTo;



    public function __construct() {
        $this->activeFrom = new \DateTime('now');
        $this->activeTo = new \DateTime('+1 year');

        $this->children = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->feedbacks = new ArrayCollection();
        $this->forBusiness = new ArrayCollection();
        $this->forState = new ArrayCollection();
        $this->forIndustry = new ArrayCollection();
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
     * @return Product
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
     * Set slug
     *
     * @param string $slug
     * @return Product
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
     * Set description
     *
     * @param string $description
     * @return Product
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
        return $this->description;
    }

    private function slugify($string)
    {
        // replace non letter or digits by -
        $string = preg_replace('~[^\\pL\d]+~u', '-', $string);

        // trim
        $string = trim($string, '-');

        // transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        // lowercase
        $string = strtolower($string);

        // remove unwanted characters
        $string = preg_replace('~[^-\w]+~', '', $string);

        return $string;
    }

    /**
     * Add users
     *
     * @param \LocalsBest\UserBundle\Entity\User $users
     * @return Product
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \LocalsBest\UserBundle\Entity\User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Product
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
     * Add feedbacks
     *
     * @param \LocalsBest\UserBundle\Entity\Feedback $feedbacks
     * @return Product
     */
    public function addFeedback(Feedback $feedbacks)
    {
        $this->feedbacks[] = $feedbacks;

        return $this;
    }

    /**
     * Remove feedbacks
     *
     * @param \LocalsBest\UserBundle\Entity\Feedback $feedbacks
     */
    public function removeFeedback(Feedback $feedbacks)
    {
        $this->feedbacks->removeElement($feedbacks);
    }

    /**
     * Get feedbacks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Add tags
     *
     * @param \LocalsBest\UserBundle\Entity\Tag $tags
     * @return Product
     */
    public function addTag(Tag $tags)
    {
        $this->tags[] = $tags;

        $tags->addProduct($this);

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \LocalsBest\UserBundle\Entity\Tag $tags
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

    /**
     * Set views
     *
     * @param integer $views
     * @return Product
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
     * Set is_system
     *
     * @param boolean $isSystem
     * @return Product
     */
    public function setIsSystem($isSystem)
    {
        $this->is_system = $isSystem;

        return $this;
    }

    /**
     * Get is_system
     *
     * @return boolean 
     */
    public function getIsSystem()
    {
        return $this->is_system;
    }

    /**
     * Set short_description
     *
     * @param string $shortDescription
     * @return Product
     */
    public function setShortDescription($shortDescription)
    {
        $this->short_description = $shortDescription;

        return $this;
    }

    /**
     * Get short_description
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->short_description;
    }

    /**
     * Set created_by
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return Product
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Product
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
     * Add images
     *
     * @param \LocalsBest\UserBundle\Entity\ProductImage $images
     * @return Product
     */
    public function addImage(ProductImage $images)
    {
        $images->setProduct($this);
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \LocalsBest\UserBundle\Entity\ProductImage $images
     */
    public function removeImage(ProductImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set activeFrom
     *
     * @param \DateTime $activeFrom
     * @return Product
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    /**
     * Get activeFrom
     *
     * @return \DateTime 
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Set activeTo
     *
     * @param \DateTime $activeTo
     * @return Product
     */
    public function setActiveTo($activeTo)
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    /**
     * Get activeTo
     *
     * @return \DateTime 
     */
    public function getActiveTo()
    {
        return $this->activeTo;
    }

    /**
     * Set prepayment
     *
     * @param boolean $prepayment
     * @return Product
     */
    public function setPrepayment($prepayment)
    {
        $this->prepayment = $prepayment;

        return $this;
    }

    /**
     * Get prepayment
     *
     * @return boolean 
     */
    public function getPrepayment()
    {
        return $this->prepayment;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set parent
     *
     * @param \LocalsBest\UserBundle\Entity\Product $parent
     * @return Product
     */
    public function setParent(Product $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \LocalsBest\UserBundle\Entity\Product 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \LocalsBest\UserBundle\Entity\Product $children
     * @return Product
     */
    public function addChild(Product $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \LocalsBest\UserBundle\Entity\Product $children
     */
    public function removeChild(Product $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add types
     *
     * @param \LocalsBest\UserBundle\Entity\ProductType $types
     * @return Product
     */
    public function addType(ProductType $types)
    {
        $this->types[] = $types;

        return $this;
    }

    /**
     * Remove types
     *
     * @param \LocalsBest\UserBundle\Entity\ProductType $types
     */
    public function removeType(ProductType $types)
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

    public function getPrice()
    {
        $types = $this->getTypes();
        $prices = [];
        foreach ($types as $type) {
            $prices[] = number_format($type->getPrice() * (1 + $type->getMargin() / 100), 0);
        }
        sort($prices);

        if (count($prices) > 1) {
            if (last($prices) <= 0) {
                return 'Free';
            }

            if ($prices[0] <= 0) {
                return 'Free-$' . last($prices);
            }

            return '$' . $prices[0] . '-$' . last($prices);
        } elseif (count($prices) == 1) {
            if ($prices[0] == 0) {
                return 'Free';
            } elseif ($prices[0] < 0) {
                return 'Contact Us for Quote';
            } else {
                return '$' . $prices[0];
            }
        } else {
            return '';
        }
    }

    /**
     * Add forBusiness
     *
     * @param \LocalsBest\UserBundle\Entity\Business $forBusiness
     * @return Product
     */
    public function addForBusiness(Business $forBusiness)
    {
        $this->forBusiness[] = $forBusiness;

        return $this;
    }

    /**
     * Remove forBusiness
     *
     * @param \LocalsBest\UserBundle\Entity\Business $forBusiness
     */
    public function removeForBusiness(Business $forBusiness)
    {
        $this->forBusiness->removeElement($forBusiness);
    }

    /**
     * Get forBusiness
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getForBusiness()
    {
        return $this->forBusiness;
    }

    /**
     * Set forPage
     *
     * @param array $forPage
     * @return Product
     */
    public function setForPage($forPage)
    {
        $this->forPage = $forPage;

        return $this;
    }

    /**
     * Get forPage
     *
     * @return array
     */
    public function getForPage()
    {
        return $this->forPage;
    }

    /**
     * Set forStatus
     *
     * @param array $forStatus
     * @return Product
     */
    public function setForStatus($forStatus)
    {
        $this->forStatus = $forStatus;

        return $this;
    }

    /**
     * Get forStatus
     *
     * @return array
     */
    public function getForStatus()
    {
        return $this->forStatus;
    }

    /**
     * Set forRoles
     *
     * @param array $forRoles
     * @return Product
     */
    public function setForRoles($forRoles)
    {
        $this->forRoles = $forRoles;

        return $this;
    }

    /**
     * Get forRoles
     *
     * @return array 
     */
    public function getForRoles()
    {
        return $this->forRoles;
    }

    /**
     * Add forState
     *
     * @param \LocalsBest\UserBundle\Entity\State $forState
     * @return Product
     */
    public function addForState(State $forState)
    {
        $this->forState[] = $forState;

        return $this;
    }

    /**
     * Remove forState
     *
     * @param \LocalsBest\UserBundle\Entity\State $forState
     */
    public function removeForState(State $forState)
    {
        $this->forState->removeElement($forState);
    }

    /**
     * Get forState
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getForState()
    {
        return $this->forState;
    }

    /**
     * Add forIndustry
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $forIndustry
     * @return Product
     */
    public function addForIndustry(IndustryType $forIndustry)
    {
        $this->forIndustry[] = $forIndustry;

        return $this;
    }

    /**
     * Remove forIndustry
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $forIndustry
     */
    public function removeForIndustry(IndustryType $forIndustry)
    {
        $this->forIndustry->removeElement($forIndustry);
    }

    /**
     * Get forIndustry
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getForIndustry()
    {
        return $this->forIndustry;
    }

    /**
     * Set is_able_for_slider
     *
     * @param boolean $isAbleForSlider
     * @return Product
     */
    public function setIsAbleForSlider($isAbleForSlider)
    {
        $this->is_able_for_slider = $isAbleForSlider;

        return $this;
    }

    /**
     * Get is_able_for_slider
     *
     * @return boolean 
     */
    public function getIsAbleForSlider()
    {
        return $this->is_able_for_slider;
    }


    /**
     * Set addon_part
     *
     * @param string $addonPart
     * @return Product
     */
    public function setAddonPart($addonPart)
    {
        $this->addon_part = $addonPart;

        return $this;
    }

    /**
     * Get addon_part
     *
     * @return string 
     */
    public function getAddonPart()
    {
        return $this->addon_part;
    }

    /**
     * Set addon_type
     *
     * @param string $addonType
     * @return Product
     */
    public function setAddonType($addonType)
    {
        $this->addon_type = $addonType;

        return $this;
    }

    /**
     * Get addon_type
     *
     * @return string 
     */
    public function getAddonType()
    {
        return $this->addon_type;
    }

    /**
     * Set linkTo
     *
     * @param string $linkTo
     * @return Product
     */
    public function setLinkTo($linkTo)
    {
        $this->linkTo = $linkTo;

        return $this;
    }

    /**
     * Get linkTo
     *
     * @return string 
     */
    public function getLinkTo()
    {
        return $this->linkTo;
    }

    /**
     * Set is_able_for_shop
     *
     * @param boolean $isAbleForShop
     * @return Product
     */
    public function setIsAbleForShop($isAbleForShop)
    {
        $this->is_able_for_shop = $isAbleForShop;

        return $this;
    }

    /**
     * Get is_able_for_shop
     *
     * @return boolean 
     */
    public function getIsAbleForShop()
    {
        return $this->is_able_for_shop;
    }
}
