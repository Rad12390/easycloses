<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\CommonBundle\Entity\Tag as CommonTag;
use LocalsBest\NotificationBundle\Entity\UserNotification;
use LocalsBest\ShopBundle\Entity\Combo;
use LocalsBest\ShopBundle\Entity\Destination;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\Package;
use LocalsBest\ShopBundle\Entity\Quotes;
use LocalsBest\ShopBundle\Entity\PaymentSplitSettings;
use LocalsBest\ShopBundle\Entity\Terms;
use LocalsBest\ShopBundle\Entity\UserOrder;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\UserBundle\Dbal\Types\StatusType;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LocalsBest\UserBundle\Entity\User
 *
 * @ORM\Table(name="users_old", uniqueConstraints={@ORM\UniqueConstraint(name="unique_username", columns={"username"})})
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", message="Username entered is already registered. Please change the User Name field and try again.")
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 * @ORM\HasLifecycleCallbacks()
 */
class User extends OAuthUser implements AdvancedUserInterface, \Serializable
{
    const TYPE_REGULAR = 1;
    const TYPE_REGULAR_ARRAY = [ 'name' => 'Regular', 'short_name' => 'R', 'color' => 'primary', ];
    const TYPE_SPECIAL = 2;
    const TYPE_SPECIAL_ARRAY = [ 'name' => 'Special', 'short_name' => 'S', 'color' => 'success', ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="firstname")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "100",
     *      minMessage = "Your first name must be at least {{ limit }} characters length",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters length"
     * )
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(name="lastname")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "100",
     *      minMessage = "Your last name must be at least {{ limit }} characters length",
     *      maxMessage = "Your last name cannot be longer than {{ limit }} characters length"
     * )
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min="4", max="64")
     * @JMS\Exclude
     * @var string
     */
    protected $password;

    /**
     * @var Email
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Email",cascade={"persist"})
     * @var \LocalsBest\UserBundle\Entity\Email
     */
    protected $primaryEmail;


    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Phone",cascade={"persist"})
     * @var \LocalsBest\UserBundle\Entity\Phone
     */
    protected $primaryPhone;

    /**
     * @ORM\Column(type="string", length=100, name="username", unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="StatusType", nullable=false)
     * @Assert\NotBlank()
     * @DoctrineAssert\Enum(entity="LocalsBest\UserBundle\Dbal\Types\StatusType")
     * @var int
     */
    protected $status;

    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Contact", inversedBy="user", cascade={"persist", "merge", "refresh", "detach"})
     * @var \LocalsBest\UserBundle\Entity\Contact
     * @Assert\Valid()
     */
    protected $contact;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Property", mappedBy="user")
     */
    protected $properties;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\BusinessCategory")
     */
    protected $businessCategories;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $rating;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=100, nullable=true )
     * @JMS\Exclude
     */
    protected $token;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Role", inversedBy="users")
     */
    protected $role;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Business", mappedBy="owner")
     */
    protected $ownedBusiness;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Business", mappedBy="staffs", cascade={"persist"})
     */
    protected $businesses;

    //*********************************************

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\User", mappedBy="myVendors")
     **/
    private $vendorsWithMe;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="vendorsWithMe")
     * @ORM\JoinTable(name="new_vendors",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="vendor_user_id", referencedColumnName="id")}
     *      )
     **/
    private $myVendors;

    /**
     * @var int
     * @ORM\Column(type="integer", name="vendorCategory", nullable=true)
     */
    protected $vendorCategory = null;

    //*********************************************

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Invite", mappedBy="createdBy")
     * @JMS\Exclude
     */
    protected $sendInvites;

    /**
     * @var string
     * @ORM\Column(name="facebook_id", unique=true, nullable=true)
     */
    protected $facebookId;

    /**
     * @var string
     * @ORM\Column(name="twitter_id", unique=true, nullable=true)
     */
    protected $twitterId;

    /**
     * @var string
     * @ORM\Column(name="linkedin_id", unique=true, nullable=true)
     */
    protected $linkedInId;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User")
     */
    protected $createdBy;

    /**
     * @Vich\UploadableField(mapping="users", fileNameProperty="fileName")
     *
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File $file
     * @Assert\Image(
     *     maxSize = "2048k",
     * )
     *
     * @JMS\AccessType("public_method")
     */
    protected $file;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, name="file_name", nullable=true )
     */
    protected $fileName;

    /**
     * @var Preference
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Preference", inversedBy="user", cascade={"persist"})
     */
    protected $preference;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $aboutMe;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $dba;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Language")
     */
    protected $languages;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\City")
     */
    protected $workingCities;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business")
     * @var \LocalsBest\UserBundle\Entity\Business
     */
    protected $owner;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\NotificationBundle\Entity\UserNotification", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     * @JMS\Exclude
     * @var ArrayCollection
     */
    protected $notifications;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Vendor", inversedBy="users", cascade={"persist"})
     */
    protected $vendors;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\LocalsBest\CommonBundle\Entity\Note", cascade={"persist", "merge", "refresh", "detach"})
     */
    protected $notes;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\LocalsBest\CommonBundle\Entity\Tag", cascade={"persist", "merge", "refresh", "detach"})
     */
    protected $tags;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Event", mappedBy="user", orphanRemoval=true, cascade={"persist", "merge", "refresh", "detach"})
     */
    protected $events;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Event", mappedBy="assignedTo")
     */
    protected $assignedTo;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Timestampable(on="create")
     * @var integer
     */
    protected $created;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Timestampable(on="update")
     * @var integer
     */
    protected $updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var integer
     */
    protected $deleted;

    

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Share", cascade={"persist", "merge", "refresh", "detach"})
     **/
   protected $shares;

   /**
    * Whether the current user is a document approver
    * @ORM\Column(name="is_docApprover", type="boolean")
    * @var int
    */
    protected $isDocumentApprover = 0;

    //***********************************

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     * @var integer
     */
    protected $stateLicenseId;

    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy"}, nullable=true)
     * @var \DateTime
     */
    protected $licenseExpirationDate;

    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy"}, nullable=true)
     * @var \DateTime
     */
    protected $birthday;

    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy"}, nullable=true)
     * @var \DateTime
     */
    protected $joinedCompanyDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $website;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $portal_user_id;

    /**
     * @var Team
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Team", inversedBy="agents")
     */
    protected $team;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\SurveyResult", mappedBy="users", cascade={"all"})
     */
    protected $results;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" = true})
     * @var boolean
     */
    protected $isPasswordDefault;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\AssociationRow", mappedBy="user")
     */
    protected $associations;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" = 1})
     * @var boolean
     */
    protected $user_type = 1;



    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PaidInvite", mappedBy="user")
     */
    private $user_for_paid_invite;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\ImportantDocument", mappedBy="user")
     */
    private $important_doc_types;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="users")
     *
     * @var ArrayCollection
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="user")
     * @var ArrayCollection
     */
    private $feedbacks;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="created_by")
     * @var ArrayCollection
     */
    private $created_products;

    /**
     * @ORM\Column(name="authorize_profile_id", type="string", length=32, nullable=true)
     */
    private $authorizeProfileId;

    /**
     * @ORM\Column(name="stripe_account_id", type="string", nullable=true)
     */
    private $stripeAccountId;

    /**
     * @ORM\Column(name="stripe_account_activated", type="boolean", nullable=true)
     */
    private $stripeAccountActivated;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $wp_website_url;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     *
     * @var array
     */
    private $wp_credentials = ['wp_username' => '', 'wp_password' => ''];

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $wp_agent_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $wp_agent_referral_url;

    /**
     * @ORM\Column(type="boolean", name="search_hidden", nullable=true, options={"default" = false})
     */
    private $searchVisible = false;
  

    /**
     * @var ClientBusiness
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\ClientBusiness", mappedBy="user")
     */
    private $client_business;

    /**
     * @var integer
     *
     * @ORM\Column(name="assign_inventory", options={"default" = 0})
     */
    private $assign_inventory = 0;

    /**
     * One User has Many Shop Items.
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Item", mappedBy="createdBy")
     */
    private $shopItems;

    /**
     * One User has Many Shop Packages.
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Package", mappedBy="createdBy")
     */
    private $shopPackages;

    /**
     * One User has Many Shop Combos.
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Combo", mappedBy="createdBy")
     */
    private $shopCombos;

    /**
     * @var integer
     *
     * @ORM\Column(name="champion_bin_size", options={"default" = 0}, nullable=true)
     */
    private $champion_bin_size = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="champion_frequency", options={"default" = 0}, nullable=true)
     */
    private $champion_frequency = 0;

    //***********************************

    /**
     * @var UserOrder
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\UserOrder", mappedBy="user")
     */
    private $shopOrders;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Destination", mappedBy="user")
     */
    private $destinations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\BusinessView", mappedBy="user")
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    protected $bio_views;



    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\PaymentSplitSettings", mappedBy="user")
     */
    private $paymentSplitSettings;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Quotes", mappedBy="userid")
     */
    private $quotedUser;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Quotes", mappedBy="vendorid")
     */
    private $vendor;

    /**
     * One User has Many Shop Items.
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Package", mappedBy="assignee")
     */
    private $packageAssignne;

    /**
     * @ORM\Column(type="boolean", name="customQuotes", options={"default" = false})
     */
    private $customQuotes = false;

    /**
     * @var terms
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\Terms",mappedBy="userid")
     */
    private $terms;
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        return true;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return ($this->status = StatusType::ACTIVE);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contact              = new Contact();
        $this->properties           = new ArrayCollection();
        $this->businessCategories   = new ArrayCollection();
        $this->ownedBusiness        = new ArrayCollection();
        $this->sendInvites          = new ArrayCollection();
        $this->preference           = new Preference();
        $this->languages            = new ArrayCollection();
        $this->workingCities        = new ArrayCollection();
        $this->vendors              = new ArrayCollection();
        $this->tags                 = new ArrayCollection();
        $this->notes                = new ArrayCollection();
        $this->events               = new ArrayCollection();
        $this->documentTypes        = new ArrayCollection();
        $this->shares               = new ArrayCollection();
        $this->associations         = new ArrayCollection();

        $this->setAboutMe('No Bio entered yet');

        $this->status = StatusType::ACTIVE;

        $this->myVendors            = new ArrayCollection();
        $this->vendorsWithMe        = new ArrayCollection();
        $this->assignedTo           = new ArrayCollection();
        $this->important_doc_types  = new ArrayCollection();
        $this->feedbacks            = new ArrayCollection();
        $this->products             = new ArrayCollection();
        $this->created_products     = new ArrayCollection();
        $this->social_profiles      = new ArrayCollection();
        $this->social_buckets       = new ArrayCollection();
        $this->social_articles       = new ArrayCollection();
        $this->created_social_articles = new ArrayCollection();

        $this->shopItems = new ArrayCollection();
        $this->shopPackages = new ArrayCollection();
        $this->shopCombos = new ArrayCollection();
        $this->shopOrders = new ArrayCollection();
        $this->socialSchedulers = new ArrayCollection();
        $this->bucket_scheduler_sets = new ArrayCollection();

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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return User
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
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set twitterId
     *
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set linkedInId
     *
     * @param string $linkedInId
     * @return User
     */
    public function setLinkedInId($linkedInId)
    {
        $this->linkedInId = $linkedInId;

        return $this;
    }

    /**
     * Get linkedInId
     *
     * @return string
     */
    public function getLinkedInId()
    {
        return $this->linkedInId;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return User
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
     * Set primaryEmail
     *
     * @param \LocalsBest\UserBundle\Entity\Email $primaryEmail
     * @return User
     */
    public function setPrimaryEmail(Email $primaryEmail = null)
    {
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    /**
     * Get primaryEmail address
     *
     * @return \LocalsBest\UserBundle\Entity\Email
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * Set primaryPhone
     *
     * @param \LocalsBest\UserBundle\Entity\Phone $primaryPhone
     * @return User
     */
    public function setPrimaryPhone(Phone $primaryPhone = null)
    {
        $this->primaryPhone = $primaryPhone;

        return $this;
    }

    /**
     * Get primaryPhone
     *
     * @return \LocalsBest\UserBundle\Entity\Phone
     */
    public function getPrimaryPhone()
    {
        return $this->primaryPhone;
    }

    /**
     * Set contact
     *
     * @param \LocalsBest\UserBundle\Entity\Contact $contact
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function setContact(Contact $contact)
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
     * Add properties
     *
     * @param \LocalsBest\UserBundle\Entity\Property $properties
     * @return User
     */
    public function addProperty(Property $properties)
    {
        $this->properties[] = $properties;

        return $this;
    }

    /**
     * Remove properties
     *
     * @param \LocalsBest\UserBundle\Entity\Property $properties
     */
    public function removeProperty(Property $properties)
    {
        $this->properties->removeElement($properties);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add businessCategories
     *
     * @param \LocalsBest\UserBundle\Entity\BusinessCategory $businessCategories
     * @return User
     */
    public function addBusinessCategory(BusinessCategory $businessCategories)
    {
        $this->businessCategories[] = $businessCategories;

        return $this;
    }

    /**
     * Remove businessCategories
     *
     * @param \LocalsBest\UserBundle\Entity\BusinessCategory $businessCategories
     */
    public function removeBusinessCategory(BusinessCategory $businessCategories)
    {
        $this->businessCategories->removeElement($businessCategories);
    }

    /**
     * Get businessCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessCategories()
    {
        return $this->businessCategories;
    }

    /**
     * Set role
     *
     * @param \LocalsBest\UserBundle\Entity\Role $role
     * @return User
     */
    public function setRole(Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \LocalsBest\UserBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $roleTitle = $this->getRole()->getRole();
        return [$roleTitle];
    }

    /**
     * Add business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return User
     */
    public function addOwnedBusiness(Business $business)
    {
        $this->ownedBusiness[] = $business;

        return $this;
    }

    /**
     * Remove business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     */
    public function removeOwnedBusiness(Business $business)
    {
        $this->ownedBusiness->removeElement($business);
    }

    /**
     * Get business
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedBusiness()
    {
        return $this->ownedBusiness;
    }

    /**
     * Add sendInvites
     *
     * @param \LocalsBest\UserBundle\Entity\Invite $sendInvites
     * @return User
     */
    public function addSendInvite(Invite $sendInvites)
    {
        $this->sendInvites[] = $sendInvites;

        return $this;
    }

    /**
     * Remove sendInvites
     *
     * @param \LocalsBest\UserBundle\Entity\Invite $sendInvites
     */
    public function removeSendInvite(Invite $sendInvites)
    {
        $this->sendInvites->removeElement($sendInvites);
    }

    /**
     * Get sendInvites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSendInvites()
    {
        return $this->sendInvites;
    }

    /**
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return User
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
     * Set preference
     *
     * @param \LocalsBest\UserBundle\Entity\Preference $preference
     * @return User
     */
    public function setPreference(Preference $preference = null)
    {
        $this->preference = $preference;

        return $this;
    }

    /**
     * Get preference
     *
     * @return \LocalsBest\UserBundle\Entity\Preference
     */
    public function getPreference()
    {
        return $this->preference;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return User
     */
    public function setFile($file = null)
    {
        if (!$file instanceof File) {
            return $this;
        }

        $this->file = $file;

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
     * Set contacts
     *
     * @param \LocalsBest\UserBundle\Entity\Contact $contacts
     * @return User
     */
    public function setContacts(Contact $contacts = null)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return \LocalsBest\UserBundle\Entity\Contact
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add businesses
     *
     * @param \LocalsBest\UserBundle\Entity\Business $businesses
     * @return User
     */
    public function addBusiness(Business $businesses)
    {
        $this->businesses[] = $businesses;

        return $this;
    }

    /**
     * Remove businesses
     *
     * @param \LocalsBest\UserBundle\Entity\Business $businesses
     */
    public function removeBusiness(Business $businesses)
    {
        $this->businesses->removeElement($businesses);
    }

    /**
     * Get businesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinesses()
    {
        return $this->businesses;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return User
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set dba
     *
     * @param string $dba
     * @return User
     */
    public function setDba($dba)
    {
        $this->dba = $dba;

        return $this;
    }

    /**
     * Get dba
     *
     * @return string
     */
    public function getDba()
    {
        return $this->dba;
    }

    /**
     * Add languages
     * @param \LocalsBest\UserBundle\Entity\Language $languages
     * @return User
     */
    public function addLanguage(Language $languages)
    {
        $this->languages[] = $languages;

        return $this;
    }

    /**
     * Remove languages
     * @param \LocalsBest\UserBundle\Entity\Language $languages
     */
    public function removeLanguage(Language $languages)
    {
        $this->languages->removeElement($languages);
    }

    /**
     * Get languages
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Add workingCities
     *
     * @param \LocalsBest\UserBundle\Entity\City $workingCities
     * @return User
     */
    public function addWorkingCities(City $workingCities)
    {
        $this->workingCities[] = $workingCities;

        return $this;
    }

    /**
     * Remove workingCities
     *
     * @param \LocalsBest\UserBundle\Entity\City $workingCities
     */
    public function removeWorkingCities(City $workingCities)
    {
        $this->workingCities->removeElement($workingCities);
    }

    /**
     * Get workingCities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkingCities()
    {
        return $this->workingCities;
    }

    public function getWorkingCitiesIds()
    {
        $result = [];

        foreach ($this->workingCities as $workingCity) {
            $result[] = $workingCity->getId();
        }

        return $result;
    }

    /**
     * Set owner
     *
     * @param \LocalsBest\UserBundle\Entity\Business $owner
     * @return User
     */
    public function setOwner(Business $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \LocalsBest\UserBundle\Entity\Business
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getInitials()
    {
        return substr($this->firstName, 0, 1) . '. ' . substr($this->lastName, 0, 1) . '.';
    }

    /**
     * Add notifications
     *
     * @param \LocalsBest\NotificationBundle\Entity\UserNotification $notifications
     * @return User
     */
    public function addNotification(UserNotification $notifications)
    {
        $this->notifications[] = $notifications;

        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \LocalsBest\NotificationBundle\Entity\UserNotification $notifications
     */
    public function removeNotification(UserNotification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @param  bool $unread
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications($unread = false)
    {
        if (!$unread) {
            return $this->notifications->filter(function($notification){

                /** @var \LocalsBest\NotificationBundle\Entity\UserNotification $notification */
                return ($notification->getRead() === false);
            });
        }

        return $this->notifications;
    }

    /**
     * Add vendors
     *
     * @param \LocalsBest\UserBundle\Entity\Vendor $vendors
     * @return User
     */
    public function addVendor(Vendor $vendors)
    {
        $this->vendors[] = $vendors;

        return $this;
    }

    /**
     * Remove vendors
     *
     * @param \LocalsBest\UserBundle\Entity\Vendor $vendors
     */
    public function removeVendor(Vendor $vendors)
    {
        $this->vendors->removeElement($vendors);
    }

    /**
     * Get vendors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * Add events
     *
     * @param \LocalsBest\UserBundle\Entity\Event $events
     * @return User
     */
    public function addEvent(Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \LocalsBest\UserBundle\Entity\Event $events
     */
    public function removeEvent(Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add tags
     *
     * @param \LocalsBest\CommonBundle\Entity\Tag $tag
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function addTag(CommonTag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \LocalsBest\CommonBundle\Entity\Tag $tag
     * @return User
     */
    public function removeTag(CommonTag $tag)
    {
        $this->tags->removeElement($tag);

        return $this;
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
     * Add notes
     *
     * @param \LocalsBest\CommonBundle\Entity\Note $note
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function addNote(Note $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove notes
     *
     * @param \LocalsBest\CommonBundle\Entity\Note $note
     * @return User
     */
    public function removeNote(Note $note)
    {
        $this->notes->remove($note);

        return $this;
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param string $updated
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set deleted
     *
     * @param \DateTime|null
     * @return string
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     * @return string
     */
    public function getDeleted()
    {
        return $this->deleted;
    }


    /**
     * Add shares
     *
     * @param \LocalsBest\UserBundle\Entity\Share $shares
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function addShare(Share $shares)
    {
        $this->shares[] = $shares;

        return $this;
    }

    /**
     * Remove shares
     *
     * @param \LocalsBest\UserBundle\Entity\Share $shares
     */
    public function removeShare(Share $shares)
    {
        $this->shares->removeElement($shares);
    }

    /**
     * Get shares
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShares()
    {
        return $this->shares;
    }

    public function isDocumentApprover()
    {
        return $this->isDocumentApprover;
    }

    public function makeDocumentApprover($isDocumentApprover = true)
    {
        $this->isDocumentApprover = $isDocumentApprover;

        return $this;
    }

    /**
     * @param mixed $vendorsWithMe
     * @return User
     */
    public function setVendorsWithMe($vendorsWithMe)
    {
        $this->vendorsWithMe[] = $vendorsWithMe;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVendorsWithMe()
    {
        return $this->vendorsWithMe;
    }

    /**
     * @return mixed
     */
    public function getMyVendors()
    {
        return $this->myVendors;
    }

    /**
     * @param mixed $myVendors
     */
    public function setMyVendors($myVendors)
    {
        $this->myVendors[] = $myVendors;
    }

    /**
     * Get assignedTo
     *
     * @return ArrayCollection
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }



    /**
     * Add assignedTo
     *
     * @param \LocalsBest\UserBundle\Entity\Event $assignedTo
     * @return User
     */
    public function addAssignedTo(Event $assignedTo)
    {
        $this->assignedTo[] = $assignedTo;

        return $this;
    }

    /**
     * Remove assignedTo
     *
     * @param \LocalsBest\UserBundle\Entity\Event $assignedTo
     */
    public function removeAssignedTo(Event $assignedTo)
    {
        $this->assignedTo->removeElement($assignedTo);
    }

    /**
     * @return mixed
     */
    public function getVendorCategory()
    {
        return $this->vendorCategory;
    }

    /**
     * @param mixed $vendorCategory
     */
    public function setVendorCategory($vendorCategory)
    {
        $this->vendorCategory = $vendorCategory;
    }

    public function getAllVendors($isWithConcierge)
    {
        $parent = $this;
        $allVendors = $this->getMyVendors()->toArray();
        $vendorIds = array();

        /** @var User $vendor */

        foreach ($allVendors as $vendor) {
            $vendorIds[] = $vendor->getId();
        }

        if(!is_null($parent->getCreatedBy()) && $this->getId() !== $parent->getCreatedBy()->getId()) {
            do {
                $parent = $parent->getCreatedBy();
                foreach($parent->getMyVendors()->toArray() as $vendor) {
                    if(!in_array($vendor->getId(), $vendorIds)) {
                        $allVendors[] = $vendor;
                        $vendorIds[] = $vendor->getId();
                    }
                }
            } while ($parent->getRole()->getRole() !== 'ROLE_ADMIN');
        }

        if ($isWithConcierge === true) {
            $allVendors[] = $parent;
        }

        $allVendors = new ArrayCollection($allVendors);

        return $allVendors;
    }

    public function getVendorsByCategory($businessType='all', $isFromMainPage=false, $isWithConcierge=false)
    {
        $allVendors = $this->getAllVendors($isWithConcierge);

        $result = array();
        /** @var \LocalsBest\UserBundle\Entity\User $vendor */
        foreach ($allVendors as $key => $vendor) {
            //Check for users that have own business
            if (count($vendor->getBusinesses())==0) {
                unset($allVendors[$key]);
                continue;
            }
            //Check free vendors for main page
            if (
                $isFromMainPage === true
                && ($vendor->getVendorCategory() === null || $vendor->getVendorCategory() < 2)
            ) {
                unset($allVendors[$key]);
                continue;
            }
            //Check for users business that have industry type
            if (count($vendor->getBusinesses()->first()->getTypes()) == 0) {
                unset($allVendors[$key]);
                continue;
            }
            if ($businessType !== 'all') {
                if (count($vendor->getBusinesses()->first()->getTypes()) == 0) {
                    unset($allVendors[$key]);
                    continue;
                }

                $have = false;
                foreach ($vendor->getBusinesses()->first()->getTypes() as $t_ype) {
                    /** @var IndustryType $t_ype */
                    if ($t_ype->getId() == ((int)$businessType)) {
                        $have = true;
                    }
                }

                if ($have == false) {
                    unset($allVendors[$key]);
                    continue;
                }
            }
            //Check for users that not a clients
            if ($vendor->getRole()->getRole() === 'ROLE_CLIENT') {
                unset($allVendors[$key]);
                continue;
            }

            $result[$vendor->getBusinesses()->first()->getTypes()->first()->getId()][] = $vendor;
        }
        $result = $this->shuffle_assoc($result);
        $result1 = array();

        foreach($result as $key => $value){

            $platinumCat = array();
            $goldCat = array();
            $silverCat = array();
            $bronzeCat = array();
            $freeCat = array();

            foreach($value as $vendor) {
                if ($vendor->getVendorCategory() == 4) {
                    $platinumCat[] = $vendor;
                    unset($allVendors[$key]);
                    continue;
                } elseif ($vendor->getVendorCategory() == 3) {
                    $goldCat[] = $vendor;
                    unset($allVendors[$key]);
                    continue;
                } elseif ($vendor->getVendorCategory() == 2) {
                    $silverCat[] = $vendor;
                    unset($allVendors[$key]);
                    continue;
                } elseif ($vendor->getVendorCategory() == 1) {
                    $bronzeCat[] = $vendor;
                    unset($allVendors[$key]);
                    continue;
                } else {
                    $freeCat[] = $vendor;
                    unset($allVendors[$key]);
                    continue;
                }
            }
            shuffle($platinumCat);
            shuffle($goldCat);
            shuffle($silverCat);
            shuffle($bronzeCat);
            shuffle($freeCat);

            $result1 = array_merge($result1, $platinumCat, $goldCat, $silverCat, $bronzeCat, $freeCat);
        }

        return new ArrayCollection($result1);
    }

    public function getVendorsByCategory2(User $businessOwner, $businessType='all', $isFromMainPage=false, $isWithConcierge=false)
    {
        $allVendors = $this->getAllVendors($isWithConcierge);

        $allVendors->add($businessOwner);

        $result = [];
        /** @var \LocalsBest\UserBundle\Entity\User $vendor */
        foreach ($allVendors as $key => $vendor) {
            //Check for users that have own business
            if (count($vendor->getBusinesses()) == 0) {
                unset($allVendors[$key]);
                continue;
            }

            //Check free vendors for main page
            if (
                $isFromMainPage === true
                && ($vendor->getVendorCategory() === null || $vendor->getVendorCategory() < 2)
            ) {
                unset($allVendors[$key]);
                continue;
            }

            //Check for users business that have industry type
            if (count($vendor->getBusinesses()->first()->getTypes()) == 0) {
                unset($allVendors[$key]);
                continue;
            }

            if ($businessType !== 'all') {
                if (count($vendor->getBusinesses()->first()->getTypes()) == 0) {
                    unset($allVendors[$key]);
                    continue;
                }

                $have = false;
                foreach ($vendor->getBusinesses()->first()->getTypes() as $t_ype) {
                    /** @var IndustryType $t_ype */
                    if ($t_ype->getId() == ((int)$businessType)) {
                        $have = true;
                    }
                }

                if ($have == false) {
                    unset($allVendors[$key]);
                    continue;
                }
            }

            //Check for users that not a clients
            if ($vendor->getRole()->getRole() === 'ROLE_CLIENT') {
                unset($allVendors[$key]);
                continue;
            }

            if (in_array($vendor, $result)) {
                unset($allVendors[$key]);
                continue;
            }

            if ($vendor->getId() == 1 && in_array($businessOwner->getOwner()->getId(), [173])) {
                unset($allVendors[$key]);
                continue;
            }

            $result[] = $vendor;
        }
        $result1 = [];

        $platinumCat = array();
        $goldCat = array();
        $silverCat = array();
        $bronzeCat = array();
        $freeCat = array();

        foreach($result as $vendor){
            if ($vendor->getVendorCategory() == 4) {
                $platinumCat[] = $vendor;
                continue;
            } elseif ($vendor->getVendorCategory() == 3) {
                $goldCat[] = $vendor;
                continue;
            } elseif ($vendor->getVendorCategory() == 2) {
                $silverCat[] = $vendor;
                continue;
            } elseif ($vendor->getVendorCategory() == 1) {
                $bronzeCat[] = $vendor;
                continue;
            } else {
                $freeCat[] = $vendor;
                continue;
            }
        }

        shuffle($platinumCat);
        shuffle($goldCat);
        shuffle($silverCat);
        shuffle($bronzeCat);
        shuffle($freeCat);

        $result1 = array_merge($result1, $platinumCat, $goldCat, $silverCat, $bronzeCat, $freeCat);

        return new ArrayCollection($result1);
    }

    public function shuffle_assoc($list)
    {
        if (!is_array($list)) return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

    public function getAbleVendorCategories($vendors = null, $isFromMainPage = false)
    {
        if ($vendors === null) {
            $vendors = $this->getAllVendors();
        }

        $catIds = array();

        /** @var \LocalsBest\UserBundle\Entity\User $vendor */
        foreach($vendors as $vendor) {
            if( $isFromMainPage === true && $vendor->getVendorCategory() < 2 ) {
                continue;
            }

            if(count($vendor->getBusinesses()) > 0 && count($vendor->getBusinesses()->first()->getTypes()) > 0) {
                foreach ($vendor->getBusinesses()->first()->getTypes() as $t_ype) {
                    /** @var IndustryType $t_ype */
                    if (!in_array($t_ype->getId(), $catIds)) {
                        $catIds[] = $t_ype->getId();
                    }
                }
            }
        }

        return $catIds;
    }

    /**
     * @return int
     */
    public function getStateLicenseId()
    {
        return $this->stateLicenseId;
    }

    /**
     * @param int $stateLicenseId
     */
    public function setStateLicenseId($stateLicenseId)
    {
        $this->stateLicenseId = $stateLicenseId;
    }

    /**
     * @return string
     */
    public function getLicenseExpirationDate()
    {
        return $this->licenseExpirationDate;
    }

    /**
     * @param string $licenseExpirationDate
     */
    public function setLicenseExpirationDate($licenseExpirationDate)
    {
        $this->licenseExpirationDate = $licenseExpirationDate;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getJoinedCompanyDate()
    {
        return $this->joinedCompanyDate;
    }

    /**
     * @param string $joinedCompanyDate
     */
    public function setJoinedCompanyDate($joinedCompanyDate)
    {
        $this->joinedCompanyDate = $joinedCompanyDate;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getPortalUserId()
    {
        return $this->portal_user_id;
    }

    /**
     * @param string $portal_user_id
     */
    public function setPortalUserId($portal_user_id)
    {
        $this->portal_user_id = $portal_user_id;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return boolean
     */
    public function isPasswordDefault()
    {
        return $this->isPasswordDefault;
    }

    /**
     * @param boolean $isPasswordDefault
     */
    public function setIsPasswordDefault($isPasswordDefault)
    {
        $this->isPasswordDefault = $isPasswordDefault;
    }

    /**
     * @return mixed
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param mixed $associations
     */
    public function setAssociations($associations)
    {
        $this->associations = $associations;
    }

    /**
     * Set user_type
     *
     * @param integer $userType
     * @return User
     */
    public function setUserType($userType)
    {
        $this->user_type = $userType;

        return $this;
    }

    /**
     * Get user_type
     *
     */
    public function getUserType()
    {
        if($this->user_type == self::TYPE_REGULAR) {
            return self::TYPE_REGULAR_ARRAY;
        }

        if($this->user_type == self::TYPE_SPECIAL) {
            return self::TYPE_SPECIAL_ARRAY;
        }

        return null;
    }

    public function getUserTypeId()
    {
        return $this->user_type;
    }

    /**
     * @return mixed
     */
    public function getUserForPaidInvite()
    {
        return $this->user_for_paid_invite;
    }

    /**
     * @param mixed $user_for_paid_invite
     */
    public function setUserForPaidInvite($user_for_paid_invite)
    {
        $this->user_for_paid_invite = $user_for_paid_invite;
    }


    public function isAdmin()
    {
        if($this->getRole()->getLevel() == 1) {
            return true;
        }
        return false;
    }
    
    /**
     * Add important_doc_types
     *
     * @param \LocalsBest\UserBundle\Entity\ImportantDocument $importantDocTypes
     * @return User
     */
    public function addImportantDocType(ImportantDocument $importantDocTypes)
    {
        $this->important_doc_types[] = $importantDocTypes;

        return $this;
    }

    /**
     * Remove important_doc_types
     *
     * @param \LocalsBest\UserBundle\Entity\ImportantDocument $importantDocTypes
     */
    public function removeImportantDocType(ImportantDocument $importantDocTypes)
    {
        $this->important_doc_types->removeElement($importantDocTypes);
    }

    /**
     * Get important_doc_types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImportantDocTypes()
    {
        return $this->important_doc_types;
    }

    /**
     * Add products
     *
     * @param \LocalsBest\UserBundle\Entity\Product $products
     * @return User
     */
    public function addProduct(Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \LocalsBest\UserBundle\Entity\Product $products
     */
    public function removeProduct(Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add feedbacks
     *
     * @param \LocalsBest\UserBundle\Entity\Feedback $feedbacks
     * @return User
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
     * Add created_products
     *
     * @param \LocalsBest\UserBundle\Entity\Product $createdProducts
     * @return User
     */
    public function addCreatedProduct(Product $createdProducts)
    {
        $this->created_products[] = $createdProducts;

        return $this;
    }

    /**
     * Remove created_products
     *
     * @param \LocalsBest\UserBundle\Entity\Product $createdProducts
     */
    public function removeCreatedProduct(Product $createdProducts)
    {
        $this->created_products->removeElement($createdProducts);
    }

    /**
     * Get created_products
     *
     * @return ArrayCollection
     */
    public function getCreatedProducts()
    {
        return $this->created_products;
    }

    /**
     * @return mixed
     */
    public function getAuthorizeProfileId()
    {
        return $this->authorizeProfileId;
    }

    /**
     * @param mixed $authorizeProfileId
     */
    public function setAuthorizeProfileId($authorizeProfileId)
    {
        $this->authorizeProfileId = $authorizeProfileId;
    }

    /**
     * @return string
     */
    public function getWpWebsiteUrl()
    {
        return $this->wp_website_url;
    }

    /**
     * @param string $wp_website_url
     *
     * @return User
     */
    public function setWpWebsiteUrl($wp_website_url)
    {
        $this->wp_website_url = $wp_website_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getWpAgentId()
    {
        return $this->wp_agent_id;
    }

    /**
     * @param string $wp_agent_id
     *
     * @return User
     */
    public function setWpAgentId($wp_agent_id)
    {
        $this->wp_agent_id = $wp_agent_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getWpAgentReferralUrl()
    {
        return $this->wp_agent_referral_url;
    }

    /**
     * @param string $wp_agent_referral_url
     *
     * @return User
     */
    public function setWpAgentReferralUrl($wp_agent_referral_url)
    {
        $this->wp_agent_referral_url = $wp_agent_referral_url;

        return $this;
    }


    /**
     * @ORM\PreRemove
     *
     * @param LifecycleEventArgs $args
     */
    public function synchronizeWPWithDelete(LifecycleEventArgs $args)
    {
        $user = $args->getObject();

        $url = $user->getWpWebsiteUrl()
            . (substr($user->getWpWebsiteUrl(), -1) == '/' ? '' : '/') .
             'wp/v2/users/' . $user->getWpAgentId();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_exec($ch); // run the whole process

        curl_close($ch);
    }

    /**
     * @return mixed
     */
    public function getSearchVisible()
    {
        return $this->searchVisible;
    }

    /**
     * @param mixed $searchVisible
     */
    public function setSearchVisible($searchVisible)
    {
        $this->searchVisible = $searchVisible;
    }

    public function getBusinessProperty()
    {
        /** @var Property $property */
        foreach ($this->getProperties() as $property) {
            if(strtolower($property->getFormat()) == 'business') {
                return $property;
            }
        }

        return null;
    }
  

    /**
     * Set isDocumentApprover
     *
     * @param boolean $isDocumentApprover
     * @return User
     */
    public function setIsDocumentApprover($isDocumentApprover)
    {
        $this->isDocumentApprover = $isDocumentApprover;

        return $this;
    }

    /**
     * Get isDocumentApprover
     *
     * @return boolean
     */
    public function getIsDocumentApprover()
    {
        return $this->isDocumentApprover;
    }

    /**
     * Get isPasswordDefault
     *
     * @return boolean
     */
    public function getIsPasswordDefault()
    {
        return $this->isPasswordDefault;
    }

    /**
     * Add vendorsWithMe
     *
     * @param \LocalsBest\UserBundle\Entity\User $vendorsWithMe
     * @return User
     */
    public function addVendorsWithMe(User $vendorsWithMe)
    {
        $this->vendorsWithMe[] = $vendorsWithMe;

        return $this;
    }

    /**
     * Remove vendorsWithMe
     *
     * @param \LocalsBest\UserBundle\Entity\User $vendorsWithMe
     */
    public function removeVendorsWithMe(User $vendorsWithMe)
    {
        $this->vendorsWithMe->removeElement($vendorsWithMe);
    }

    /**
     * Add myVendors
     *
     * @param \LocalsBest\UserBundle\Entity\User $myVendors
     * @return User
     */
    public function addMyVendor(User $myVendors)
    {
        $this->myVendors[] = $myVendors;

        return $this;
    }

    /**
     * Remove myVendors
     *
     * @param \LocalsBest\UserBundle\Entity\User $myVendors
     */
    public function removeMyVendor(User $myVendors)
    {
        $this->myVendors->removeElement($myVendors);
    }

    
    /**
     * Add workingCities
     *
     * @param \LocalsBest\UserBundle\Entity\City $workingCities
     * @return User
     */
    public function addWorkingCity(City $workingCities)
    {
        $this->workingCities[] = $workingCities;

        return $this;
    }

    /**
     * Remove workingCities
     *
     * @param \LocalsBest\UserBundle\Entity\City $workingCities
     */
    public function removeWorkingCity(City $workingCities)
    {
        $this->workingCities->removeElement($workingCities);
    }

    /**
     * Add results
     *
     * @param \LocalsBest\UserBundle\Entity\SurveyResult $results
     * @return User
     */
    public function addResult(SurveyResult $results)
    {
        $this->results[] = $results;

        return $this;
    }

    /**
     * Remove results
     *
     * @param \LocalsBest\UserBundle\Entity\SurveyResult $results
     */
    public function removeResult(SurveyResult $results)
    {
        $this->results->removeElement($results);
    }

    /**
     * Add associations
     *
     * @param \LocalsBest\UserBundle\Entity\AssociationRow $associations
     * @return User
     */
    public function addAssociation(AssociationRow $associations)
    {
        $this->associations[] = $associations;

        return $this;
    }

    /**
     * Remove associations
     *
     * @param \LocalsBest\UserBundle\Entity\AssociationRow $associations
     */
    public function removeAssociation(AssociationRow $associations)
    {
        $this->associations->removeElement($associations);
    }

    /**
     * Add user_for_paid_invite
     *
     * @param \LocalsBest\UserBundle\Entity\PaidInvite $userForPaidInvite
     * @return User
     */
    public function addUserForPaidInvite(PaidInvite $userForPaidInvite)
    {
        $this->user_for_paid_invite[] = $userForPaidInvite;

        return $this;
    }

    /**
     * Remove user_for_paid_invite
     *
     * @param \LocalsBest\UserBundle\Entity\PaidInvite $userForPaidInvite
     */
    public function removeUserForPaidInvite(PaidInvite $userForPaidInvite)
    {
        $this->user_for_paid_invite->removeElement($userForPaidInvite);
    }

    /**
     * @return ClientBusiness
     */
    public function getClientBusiness()
    {
        return $this->client_business;
    }

    /**
     * @param ClientBusiness $client_business
     */
    public function setClientBusiness($client_business)
    {
        $this->client_business = $client_business;
    }

    /**
     * @return int
     */
    public function getAssignInventory()
    {
        return $this->assign_inventory;
    }

    /**
     * @param int $assign_inventory
     */
    public function setAssignInventory($assign_inventory)
    {
        $this->assign_inventory = $assign_inventory;
    }

    /**
     * Set wp_credentials
     *
     * @param array $wpCredentials
     * @return User
     */
    public function setWpCredentials($wpCredentials)
    {
        $this->wp_credentials = $wpCredentials;

        return $this;
    }

    /**
     * Get wp_credentials
     *
     * @return array
     */
    public function getWpCredentials()
    {
        return $this->wp_credentials;
    }

    /**
     * Add shopItems
     *
     * @param \LocalsBest\ShopBundle\Entity\Item $shopItems
     * @return User
     */
    public function addShopItem(Item $shopItems)
    {
        $this->shopItems[] = $shopItems;

        return $this;
    }

    /**
     * Remove shopItems
     *
     * @param \LocalsBest\ShopBundle\Entity\Item $shopItems
     */
    public function removeShopItem(Item $shopItems)
    {
        $this->shopItems->removeElement($shopItems);
    }

    /**
     * Get shopItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShopItems()
    {
        return $this->shopItems;
    }

    /**
     * Add shopPackages
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $shopPackages
     * @return User
     */
    public function addShopPackage(Package $shopPackages)
    {
        $this->shopPackages[] = $shopPackages;

        return $this;
    }

    /**
     * Remove shopPackages
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $shopPackages
     */
    public function removeShopPackage(Package $shopPackages)
    {
        $this->shopPackages->removeElement($shopPackages);
    }

    /**
     * Get shopPackages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShopPackages()
    {
        return $this->shopPackages;
    }

    /**
     * Add shopCombos
     *
     * @param \LocalsBest\ShopBundle\Entity\Combo $shopCombos
     * @return User
     */
    public function addShopCombo(Combo $shopCombos)
    {
        $this->shopCombos[] = $shopCombos;

        return $this;
    }

    /**
     * Remove shopCombos
     *
     * @param \LocalsBest\ShopBundle\Entity\Combo $shopCombos
     */
    public function removeShopCombo(Combo $shopCombos)
    {
        $this->shopCombos->removeElement($shopCombos);
    }

    /**
     * Get shopCombos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShopCombos()
    {
        return $this->shopCombos;
    }

    /**
     * Add shopOrders
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $shopOrders
     * @return User
     */
    public function addShopOrder(UserOrder $shopOrders)
    {
        $this->shopOrders[] = $shopOrders;

        return $this;
    }

    /**
     * Remove shopOrders
     *
     * @param \LocalsBest\ShopBundle\Entity\UserOrder $shopOrders
     */
    public function removeShopOrder(UserOrder $shopOrders)
    {
        $this->shopOrders->removeElement($shopOrders);
    }

    /**
     * Get shopOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShopOrders()
    {
        return $this->shopOrders;
    }

    /**
     * @return int
     */
    public function getChampionBinSize()
    {
        return $this->champion_bin_size;
    }

    /**
     * @param int $champion_bin_size
     */
    public function setChampionBinSize($champion_bin_size)
    {
        $this->champion_bin_size = $champion_bin_size;
    }

    /**
     * @return mixed
     */
    public function getStripeAccountId()
    {
        return $this->stripeAccountId;
    }

    /**
     * @param mixed $accountId
     */
    public function setStripeAccountId($accountId)
    {
        $this->stripeAccountId = $accountId;
    }

    /**
     * @return mixed
     */
    public function getStripeAccountActivated()
    {
        return $this->stripeAccountActivated;
    }

    /**
     * @param mixed $stripeAccountActivated
     */
    public function setStripeAccountActivated($stripeAccountActivated)
    {
        $this->stripeAccountActivated = $stripeAccountActivated;
    }

    /**
     * @return int
     */
    public function getChampionFrequency()
    {
        return $this->champion_frequency;
    }

    /**
     * @param int $champion_frequency
     */
    public function setChampionFrequency($champion_frequency)
    {
        $this->champion_frequency = $champion_frequency;
    }


    /**
     * Add destination
     *
     * @param \LocalsBest\ShopBundle\Entity\Destination $destination
     * @return User
     */
    public function addDestination(Destination $destination)
    {
        $this->destinations[] = $destination;

        return $this;
    }

    /**
     * Remove destination
     *
     * @param \LocalsBest\ShopBundle\Entity\Destination $destination
     */
    public function removeDestination(Destination $destination)
    {
        $this->destinations->removeElement($destination);
    }

    /**
     * Get destinations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    /**
     * Add bio view
     *
     * @param \LocalsBest\UserBundle\Entity\BusinessView $bioView
     *
     * @return User
     */
    public function addBioView(BusinessView $bioView)
    {
        $this->bio_views[] = $bioView;

        return $this;
    }

    /**
     * Remove bio view
     *
     * @param \LocalsBest\UserBundle\Entity\BusinessView $bioView
     */
    public function removeBioView(BusinessView $bioView)
    {
        $this->bio_views->removeElement($bioView);
    }

    /**
     * Get bio views
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBioViews()
    {
        return $this->bio_views;
    }


    /**
     * Set PaymentSplitSettings
     *
     * @param \LocalsBest\ShopBundle\Entity\PaymentSplitSettings $paymentSplitSettings
     * @return User
     */
    public function setPaymentSplitSettings(PaymentSplitSettings $paymentSplitSettings = null)
    {
        $this->paymentSplitSettings = $paymentSplitSettings;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getPaymentSplitSettings()
    {
        return $this->paymentSplitSettings;
    }

    /**
     * Set quotedUser
     *
     * @param \LocalsBest\ShopBundle\Entity\Quotes $quotedUser
     * @return User
     */
    public function setQuotedUser(Quotes $quotedUser = null)
    {
        $this->quotedUser = $quotedUser;

        return $this;
    }

    /**
     * Get quotedUser
     *
     * @return \LocalsBest\ShopBundle\Entity\Quotes
     */
    public function getQuotedUser()
    {
        return $this->quotedUser;
    }

    /**
     * Set vendor
     *
     * @param \LocalsBest\ShopBundle\Entity\Quotes $vendor
     */
    public function setVendor(Quotes $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \LocalsBest\ShopBundle\Entity\Quotes
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set customQuotes
     *
     * @return \boolean
     */
    public function setCustomQuotes($customQuotes)
    {
        $this->customQuotes = $customQuotes;

        return $this;
    }

    /**
     * Get customQuotes
     *
     * @return \boolean
     */
    public function getCustomQuotes()
    {
        return $this->customQuotes;
    }

    public function getTerms()
    {
        return $this->terms;
    }
    /**
     * Set terms
     *
     * @param \LocalsBest\ShopBundle\Entity\Terms $terms
     * @return terms
     */
    public function setTerms(Terms $terms = null)
    {
        $this->terms = $terms;

        return $this;
    }
}
