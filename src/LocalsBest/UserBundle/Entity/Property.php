<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use LocalsBest\UserBundle\Dbal\Types\PropertyTypeType;

/**
 * LocalsBest\UserBundle\Entity\Property
 *
 * @ORM\Table(name="properties")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PropertyRepository")
 */
class Property
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="properties")
     * @var User
     */
    protected $user;
    
    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Address", cascade={"all"})
     * @Assert\Valid()
     */
    protected $address;
    
    /**
     * @ORM\Column(type="PropertyTypeType", nullable=true)
     * @DoctrineAssert\Enum(entity="LocalsBest\UserBundle\Dbal\Types\PropertyTypeType")
     */
    protected $type;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_public", nullable=true)
     */
    protected $isPublic;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="format", nullable=true)
     */
    protected $format;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="title", nullable=true)
     */
    protected $title;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->address  = new Address();
        $this->type = PropertyTypeType::SINGLE;
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
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return Property
     */
    public function setUser(\LocalsBest\UserBundle\Entity\User $user = null)
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
     * Set type
     *
     * @param string $type
     *
     * @return Property
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set address
     *
     * @param \LocalsBest\UserBundle\Entity\Address $address
     * @return Property
     */
    public function setAddress(\LocalsBest\UserBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \LocalsBest\UserBundle\Entity\Address 
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * Set isPublic
     *
     * @param boolean $isPublic
     * @return Property
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean 
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return Property
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string 
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Property
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
}
