<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\UserBundle\Dbal\Types\InviteStatusType;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LocalsBest\UserBundle\Entity\Invite
 *
 * @ORM\Table(name="invites")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\InviteRepository")
 * 
 */
class Invite 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=100, name="email")
     * @Assert\NotBlank()
     * @Assert\Email
     */
    protected $email;
    
    /**
     * @ORM\Column(type="InviteStatusType", nullable=false)
     * @Assert\NotBlank()
     * @DoctrineAssert\Enum(entity="LocalsBest\UserBundle\Dbal\Types\InviteStatusType")
     */
    protected $status;
    
    /**
     * @ORM\Column(name="token", type="string", length=100, nullable=true )
     */
    protected $token;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Role")
     * @var Role
     */
    protected $role;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="sendInvites")
     * @var User
     */
    protected $createdBy;
    
    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Timestampable(on="create")
     * @var integer
     */
    protected $created;
    
    /**
     * Invite Business
     * 
     * @var Business
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business")
     */
    protected $business;


    /**
     * @ORM\ManyToOne(targetEntity="IndustryType")
     * @ORM\JoinColumn(name="industry_type_id", referencedColumnName="id")
     */
    private $industryType;


    public function __construct()
    {
        $this->status = InviteStatusType::INVITE;
        
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
     * Set email
     *
     * @param string $email
     * @return Invite
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
     * Set token
     *
     * @param string $token
     * @return Invite
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
     * Set role
     *
     * @param \LocalsBest\UserBundle\Entity\Role $role
     * @return Invite
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
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return Invite
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
     * Set status
     *
     * @param int status
     * @return Invite
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return Invite
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
    
    public function getBusiness()
    {
        return $this->business;
    }
    
    public function setBusiness(Business $business)
    {
        $this->business = $business;
        
        return $this;
    }

    /**
     * Set industryType
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industryType
     * @return Invite
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
}