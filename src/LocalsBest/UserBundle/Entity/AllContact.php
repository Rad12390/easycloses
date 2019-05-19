<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LocalsBest\CommonBundle\Entity\BaseEntity;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * LocalsBest\UserBundle\Entity\AllContact
 *
 * @ORM\Table(name="allContacts")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\AllContactRepository")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 */
class AllContact extends BaseEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column
     */
    protected $name;
    
    /**
     * @ORM\Column(length=128)
     */
    protected $slug;
    
    /**
     * @ORM\Column(name="firstname")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "100",
     *      minMessage = "Your first name must be at least {{ limit }} characters length",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters length"
     * )
     * @var String 
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
     * @var String 
     */
    protected $lastName;
    
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
    protected $number;
    
    /**
     * @ORM\Column(type="string", length=100, name="email", nullable=true)
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="business_name", nullable=true)
     */
    protected $business_name;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\Length(
     *      min = "7",
     *      max = "20",
     *      minMessage = "Business telephone must be at least {{ limit }} characters length",
     *      maxMessage = "Business telephone cannot be longer than {{ limit }} characters length"
     * )
     *  @Assert\Regex(
     *     pattern="/\d/",
     *     match=true,
     *     message="Your phone number should contain numbers only"
     * )
     * @var Integer
     */
    protected $business_phone;

    /**
     * @ORM\Column(type="string", length=100, name="business_email", nullable=true)
     * @Assert\Email
     */
    protected $business_email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $business_address_street;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $business_address_unit;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $business_address_city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $business_address_state;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $business_address_zip;

    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Event", mappedBy="allContact", cascade={"all"}, orphanRemoval=true)
     */
    protected $events;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $invitation;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", cascade="persist")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\SubjectProperty", mappedBy="contact")
     */
    protected $subject_property;

    /**
     * @ORM\Column(length=128, nullable=true, name="category")
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assign_contacts")
     * @ORM\JoinColumn(name="assign_to", referencedColumnName="id")
     */
    protected $assign_to;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="generated_to", referencedColumnName="id")
     */
    protected $generated_by;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" = false})
     */
    protected $is_pending = false;

    /**
     * @ORM\Column(type="string", length=32, name="type", nullable=false, options={"default" = "contact"})
     */
    protected $type = 'contact';

    /**
     * @ORM\Column(type="boolean", name="is_active", options={"default" = true})
     */
    protected $is_active = true;

    /**
     * @ORM\Column(type="string", length=100, name="source", nullable=true)
     */
    protected $source;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $wp_id;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\WordPressApiBundle\Entity\Content", mappedBy="contact")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $wp_contents;

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
     * @return AllContact
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
     * @return AllContact
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
     * Get full name
     * 
     * @return string Contact's full name
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function __construct()
    {
        $this->setName('contact');
        $this->events   = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->shares   = new ArrayCollection();
    }


    /**
     * Set name
     *
     * @param string $name
     * @return AllContact
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
     * @return AllContact
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
     * Set number
     *
     * @param string $number
     * @return AllContact
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
        $number = $this->clearPhoneNumber($this->number);

        $result = strlen($number) > 0
            ? ('('.substr($number, 0, 3).') '.substr($number, 3, 3).'-'.substr($number, 6, 4))
            : '';
        return $result;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return AllContact
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
     * Add events
     *
     * @param \LocalsBest\UserBundle\Entity\Event $events
     * @return AllContact
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
     * Get invitation
     *
     * @return string 
     */
    public function getInvitation()
    {
        return $this->invitation;
    }
    
    /**
     * Set invitation
     *
     * @param string $invitation
     * @return AllContact
     */
    public function setInvitation($invitation)
    {
        $this->invitation = $invitation;

        return $this;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return AllContact
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    protected function clearPhoneNumber($number)
    {
        return str_replace(['(', ')', '-', ' ', '.'], '', $number);
    }

    /**
     * @return mixed
     */
    public function getSubjectProperty()
    {
        return $this->subject_property;
    }

    /**
     * @param mixed $subject_property
     */
    public function setSubjectProperty($subject_property)
    {
        $this->subject_property = $subject_property;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return AllContact
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssignTo()
    {
        if($this->assign_to === null) {
            return $this->getGeneratedBy();
        }

        return $this->assign_to;
    }

    /**
     * @param mixed $assign_to
     */
    public function setAssignTo($assign_to)
    {
        $this->assign_to = $assign_to;
    }

    /**
     * @return mixed
     */
    public function isPending()
    {
        return $this->is_pending;
    }

    /**
     * @param mixed $is_pending
     */
    public function setIsPending($is_pending)
    {
        $this->is_pending = $is_pending;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getGeneratedBy()
    {
        if($this->generated_by === null) {
            return $this->createdBy;
        }

        return $this->generated_by;
    }

    /**
     * @param mixed $generated_by
     */
    public function setGeneratedBy($generated_by)
    {
        $this->generated_by = $generated_by;
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * @param mixed $is_active
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }

    /**
     * @return mixed
     */
    public function getWpId()
    {
        return $this->wp_id;
    }

    /**
     * @param mixed $wp_id
     */
    public function setWpId($wp_id)
    {
        $this->wp_id = $wp_id;
    }

    /**
     * Get is_pending
     *
     * @return boolean 
     */
    public function getIsPending()
    {
        return $this->is_pending;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Add subject_property
     *
     * @param \LocalsBest\UserBundle\Entity\SubjectProperty $subjectProperty
     * @return AllContact
     */
    public function addSubjectProperty(\LocalsBest\UserBundle\Entity\SubjectProperty $subjectProperty)
    {
        $this->subject_property[] = $subjectProperty;

        return $this;
    }

    /**
     * Remove subject_property
     *
     * @param \LocalsBest\UserBundle\Entity\SubjectProperty $subjectProperty
     */
    public function removeSubjectProperty(SubjectProperty $subjectProperty)
    {
        $this->subject_property->removeElement($subjectProperty);
    }

    /**
     * Add wp_contents
     *
     * @param \LocalsBest\WordPressApiBundle\Entity\Content $wpContents
     * @return AllContact
     */
    public function addWpContent(\LocalsBest\WordPressApiBundle\Entity\Content $wpContents)
    {
        $this->wp_contents[] = $wpContents;

        return $this;
    }

    /**
     * Remove wp_contents
     *
     * @param \LocalsBest\WordPressApiBundle\Entity\Content $wpContents
     */
    public function removeWpContent(\LocalsBest\WordPressApiBundle\Entity\Content $wpContents)
    {
        $this->wp_contents->removeElement($wpContents);
    }

    /**
     * Get wp_contents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWpContents()
    {
        return $this->wp_contents;
    }

    /**
     * Set updatedBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $updatedBy
     * @return AllContact
     */
    public function setUpdatedBy(User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set business_name
     *
     * @param string $businessName
     * @return AllContact
     */
    public function setBusinessName($businessName)
    {
        $this->business_name = $businessName;

        return $this;
    }

    /**
     * Get business_name
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->business_name;
    }

    /**
     * Set business_phone
     *
     * @param string $businessPhone
     * @return AllContact
     */
    public function setBusinessPhone($businessPhone)
    {
        $this->business_phone = $businessPhone;

        return $this;
    }

    /**
     * Get business_phone
     *
     * @return string 
     */
    public function getBusinessPhone()
    {
        return $this->business_phone;
    }

    /**
     * Set business_email
     *
     * @param string $businessEmail
     * @return AllContact
     */
    public function setBusinessEmail($businessEmail)
    {
        $this->business_email = $businessEmail;

        return $this;
    }

    /**
     * Get business_email
     *
     * @return string 
     */
    public function getBusinessEmail()
    {
        return $this->business_email;
    }

    /**
     * Set business_address_street
     *
     * @param string $businessAddressStreet
     * @return AllContact
     */
    public function setBusinessAddressStreet($businessAddressStreet)
    {
        $this->business_address_street = $businessAddressStreet;

        return $this;
    }

    /**
     * Get business_address_street
     *
     * @return string 
     */
    public function getBusinessAddressStreet()
    {
        return $this->business_address_street;
    }

    /**
     * Set business_address_unit
     *
     * @param string $businessAddressUnit
     * @return AllContact
     */
    public function setBusinessAddressUnit($businessAddressUnit)
    {
        $this->business_address_unit = $businessAddressUnit;

        return $this;
    }

    /**
     * Get business_address_unit
     *
     * @return string 
     */
    public function getBusinessAddressUnit()
    {
        return $this->business_address_unit;
    }

    /**
     * Set business_address_city
     *
     * @param string $businessAddressCity
     * @return AllContact
     */
    public function setBusinessAddressCity($businessAddressCity)
    {
        $this->business_address_city = $businessAddressCity;

        return $this;
    }

    /**
     * Get business_address_city
     *
     * @return string 
     */
    public function getBusinessAddressCity()
    {
        return $this->business_address_city;
    }

    /**
     * Set business_address_state
     *
     * @param string $businessAddressState
     * @return AllContact
     */
    public function setBusinessAddressState($businessAddressState)
    {
        $this->business_address_state = $businessAddressState;

        return $this;
    }

    /**
     * Get business_address_state
     *
     * @return string 
     */
    public function getBusinessAddressState()
    {
        return $this->business_address_state;
    }

    /**
     * Set business_address_zip
     *
     * @param string $businessAddressZip
     * @return AllContact
     */
    public function setBusinessAddressZip($businessAddressZip)
    {
        $this->business_address_zip = $businessAddressZip;

        return $this;
    }

    /**
     * Get business_address_zip
     *
     * @return string 
     */
    public function getBusinessAddressZip()
    {
        return $this->business_address_zip;
    }
}
