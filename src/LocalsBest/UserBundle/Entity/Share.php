<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\UserBundle\DBAL\Types\ObjectTypeType;


/**
 * Document Share entity
 *
 * @ORM\Table(name="shares" )
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ShareRepository")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 * 
 */
class Share
{
    const STATUS_ACCEPT     = 1;
    const STATUS_REJECT     = -1;
    const STATUS_SHARE      = 0;
    const STATUS_APPROVED   = 2;
    const STATUS_REJECTED   = -2;
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * The user for this share
     * @var \LocalsBest\UserBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", )
     */
    protected $user;
    
    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Timestampable(on="create")
     * @var integer
     */
    protected $created;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @var integer
     */
    protected $updated;
    
    /**
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
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
     * @ORM\Column(name="token", type="string", length=100, nullable=true )
     */
    protected $token;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $status;
    
    /**
     * @ORM\Column(nullable=true)
     * 
     */
    protected $objectType;
    
    /**
     * Object ID
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $objectId;
    
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $readReciept;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $furtherAction;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isVisible = true;
    
    
    public function __construct()
    {
        $this->status   = self::STATUS_SHARE;
        $this->readReciept = FALSE;
        $this->furtherAction = FALSE;
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
     * Set created
     *
     * @param integer $created
     * @return DocumentShare
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
     * @param integer $updated
     * @return DocumentShare
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return integer 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return DocumentShare
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
     * Set updatedBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $updatedBy
     * @return DocumentShare
     */
    public function setUpdatedBy(\LocalsBest\UserBundle\Entity\User $updatedBy = null)
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
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return DocumentShare
     */
    public function setUser($user)
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

    /**
     * Set token
     *
     * @param string $token
     * @return DocumentShare
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
     * Set status
     *
     * @param string $status
     * @return DocumentShare
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
     * Set objectId
     *
     * @param integer $objectId
     * @return Note
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectType
     *
     * @param string $objectType
     * @return Share
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * Get objectType
     *
     * @return string 
     */
    public function getObjectType()
    {
        return $this->objectType;
    }
    
    /**
     * Set readReciept
     *
     * @param boolean $readReciept
     * @return Share
     */
    public function setReadReciept($readReciept)
    {
        $this->readReciept = $readReciept;

        return $this;
    }

    /**
     * Get readReciept
     *
     * @return boolean 
     */
    public function getReadReciept()
    {
        return $this->readReciept;
    }
    
    /**
     * Set furtherAction
     *
     * @param boolean $furtherAction
     * @return Share
     */
    public function setFurtherAction($furtherAction)
    {
        $this->furtherAction = $furtherAction;

        return $this;
    }

    /**
     * Get furtherAction
     *
     * @return boolean 
     */
    public function getFurtherAction()
    {
        return $this->furtherAction;
    }

    /**
     * @return mixed
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param mixed $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }
}
