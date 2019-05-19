<?php

namespace LocalsBest\MedAppUserBundle\Entity;

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
use LocalsBest\MedAppUserBundle\Dbal\Types\StatusType;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LocalsBest\MedAppUserBundle\Entity\User
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="unique_username", columns={"username"})})
 * @ORM\Entity(repositoryClass="LocalsBest\MedAppUserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", message="Username entered is already registered. Please change the User Name field and try again.")
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 * @ORM\HasLifecycleCallbacks()
 */
class User extends OAuthUser implements AdvancedUserInterface, \Serializable
{
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
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="email")
     * @Assert\NotBlank()
     * @Assert\Email
     */
    protected $email;


    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "7",
     *      max = "20",
     *      minMessage = "Your telephone must be at least {{ limit }} characters length",
     *      maxMessage = "Your telephone cannot be longer than {{ limit }} characters length"
     * )
     *  @Assert\Regex(
     *     pattern="/\d/",
     *     match=true,
     *     message="Your phone number should contain numbers only"
     * )
     * @var Integer 
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=100, name="username", unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="StatusType", nullable=false)
     * @Assert\NotBlank()
     * @DoctrineAssert\Enum(entity="LocalsBest\MeadAppUserBundle\Dbal\Types\StatusType")
     * @var int
     */
    protected $status;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=100, nullable=true )
     * @JMS\Exclude
     */
    protected $token;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="LocalsBest\MedAppUserBundle\Entity\Role", inversedBy="users")
     */
    protected $role;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="LocalsBest\MedAppUserBundle\Entity\User")
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
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy"}, nullable=true)
     * @var \DateTime
     */
    protected $birthday;

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
        $this->status = StatusType::ACTIVE;
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
     * @return string
     * @return User
     */
    public function setPrimaryEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get primaryEmail address
     *
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->email;
    }

    /**
     * Set primaryPhone
     *
     * 
     * @return string
     */
    public function setPrimaryPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get primaryPhone
     *
     * @return string
     */
    public function getPrimaryPhone()
    {
        return $this->phone;
    }

    /**
     * Set role
     *
     * @param \LocalsBest\MedAppUserBundle\Entity\Role $role
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
     * @return \LocalsBest\MedAppUserBundle\Entity\Role
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

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getInitials()
    {
        return substr($this->firstName, 0, 1) . '. ' . substr($this->lastName, 0, 1) . '.';
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
}
