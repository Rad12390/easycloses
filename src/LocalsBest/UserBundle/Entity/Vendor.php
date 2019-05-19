<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LocalsBest\CommonBundle\Entity\BaseEntity;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\UserBundle\Dbal\Types\StatusType;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as JMS;



/**
 * LocalsBest\UserBundle\Entity\Vendor
 *
 * @ORM\Table(name="vendors")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\VendorRepository")
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 */
class Vendor extends OAuthUser implements AdvancedUserInterface, \Serializable
{
    const CATEGORY_GOLD     = 3;
    
    const CATEGORY_SILVER   = 2;
    
    const CATEGORY_BRONZE   = 1;
    
    const CATEGORY_FREE     = 0;
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @JMS\Exclude
     */
    protected $password;

    /**
     * 
     *
     */
    protected $role;


    /**
     * @ORM\Column
     * 
     */
    protected $name;
    
    /**
     * @Vich\UploadableField(mapping="uploads", fileNameProperty="fileName")
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
     * @ORM\Column(length=128)
     */
    protected $slug;
    
    /**
     * @ORM\Column(type="string", length=100, name="business_name")
     */
    protected $businessName;
    
    /**
     * @ORM\Column(type="string", length=100, name="contact_name")
     */
    protected $contactName;
    
    /**
     * @ORM\Column(nullable=true)
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
    protected $number;
    
    /**
     * @ORM\Column(type="string", length=100, name="email", nullable=true)
     * @Assert\Email
     */
    protected $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\IndustryType")
     * @var type 
     */
    protected $businessType;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $category;
    
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\User", mappedBy="vendors", cascade={"persist","remove"})
     */
    protected $users;
    
    

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $active;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var integer
     */
    protected $deleted;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User")
     * @var type 
     */
    protected $createdBy;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User")
     * @var type 
     */
    protected $updatedBy;
    
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
     * @ORM\OneToMany(targetEntity="LocalsBest\NotificationBundle\Entity\UserNotification", mappedBy="user")
     * @JMS\Exclude
     * @var ArrayCollection
     */
    protected $notifications;
    
    public function __construct()
    {
        $this->setName('vendor');

        $this->jobs = new \Doctrine\Common\Collections\ArrayCollection();

        if (!$this->role) {
            $vendorRole = new Role();

            $vendorRole->setRole(Role::ROLE_VENDOR)
                    ->setName(Role::NAME_VENDOR);

            $this->role = $vendorRole;
            $this->active = 0;
        }
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
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
//            $this->username,
//            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
//            $this->username,
//            $this->password,
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
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

//    /**
//     * @inheritDoc
//     */
//    public function getPassword()
//    {
//        return $this->password;
//    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        return true;
    }
    

    /**
     * Set name
     *
     * @param string $name
     * @return Vendor
     */
    public function setName($name)
    {
        $this->name = $name;
        
        // replace non letter or digits by -
        $name = preg_replace('~[^\\pL\d]+~u', '-', $name);

        // trim
        $name = trim($name, '-');

        // transliterate
        $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);

        // lowercase
        $name = strtolower($name);

        // remove unwanted characters
        $name = preg_replace('~[^-\w]+~', '', $name);
        
        $slug = $name ."-". time();
        
        $this->slug = $slug;

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
     * Set slug
     *
     * @param string $slug
     * @return Vendor
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
     * Set active
     *
     * @param int $active
     * @return Vendor
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return User
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
     * Set businessName
     *
     * @param string $businessName
     * @return Vendor
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return Vendor
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
     * Set number
     *
     * @param string $number
     * @return Vendor
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Vendor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Vendor
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set updated
     *
     * @param string $updated
     * @return Vendor
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
     * Set businessType
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $businessType
     * @return Vendor
     */
    public function setBusinessType(\LocalsBest\UserBundle\Entity\IndustryType $businessType = null)
    {
        $this->businessType = $businessType;

        return $this;
    }

    /**
     * Get businessType
     *
     * @return \LocalsBest\UserBundle\Entity\IndustryType 
     */
    public function getBusinessType()
    {
        return $this->businessType;
    }

    /**
     * Add users
     *
     * @param \LocalsBest\UserBundle\Entity\User $users
     * @return Vendor
     */
    public function addUser(\LocalsBest\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \LocalsBest\UserBundle\Entity\User $users
     */
    public function removeUser(\LocalsBest\UserBundle\Entity\User $users)
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile($file = null)
    {
        if ($file instanceof \Symfony\Component\HttpFoundation\File\File) {
            $this->file = $file;

            $this->fileName = $file->getFilename();
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
     * Set category
     *
     * @param string $category
     * @return Vendor
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Vendor
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
     * Set password
     *
     * @param string $password
     * @return Vendor
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    
    /**
     * Set role
     *
     * @param \LocalsBest\UserBundle\Entity\Role $role
     * @return User
     */
    public function setRole(\LocalsBest\UserBundle\Entity\Role $role = null)
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
        if (!$this->role) {
            $vendorRole = new Role();
        
            $vendorRole->setRole('ROLE_VENDOR')
                    ->setName(Role::NAME_VENDOR);

            $this->role = $vendorRole;
        }
        
        return $this->role;
    }
    
    public function getBusinesses()
    {
        return [];
    }
    
    /**
     * Add notifications
     *
     * @param \LocalsBest\NotificationBundle\Entity\UserNotification $notifications
     * @return User
     */
    public function addNotification(\LocalsBest\NotificationBundle\Entity\UserNotification $notifications)
    {
        $this->notifications[] = $notifications;

        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \LocalsBest\NotificationBundle\Entity\UserNotification $notifications
     */
    public function removeNotification(\LocalsBest\NotificationBundle\Entity\UserNotification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }
    
    public function getNotifications($unread = false)
    {
        if (!$unread) {
            return $this->notifications->filter(function($notification){
                return ($notification->getRead() === false);
            });
        }
        
        return $this->notifications;
    }
}
