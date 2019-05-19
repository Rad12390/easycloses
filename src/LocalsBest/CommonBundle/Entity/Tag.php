<?php

namespace LocalsBest\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * The Note entity
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="LocalsBest\CommonBundle\Entity\TagRepository")
 */
class Tag extends BaseEntity
{
    /**
     * The note text
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $tag;
    
    /**
     * @Gedmo\Slug(fields={"tag", "id"}, updatable=true)
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * The note user
     * @var \LocalsBest\UserBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="\LocalsBest\UserBundle\Entity\User")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="ObjectTypeType", nullable=true)
     * @DoctrineAssert\Enum(entity="LocalsBest\UserBundle\Dbal\Types\ObjectTypeType")
     */
    protected $objectType;
    
    /**
     * Object ID
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $objectId;
    
    public function __construct($tag = null) 
    {
        $this->tag = $tag;
    }

        /**
     * Set note
     *
     * @param string $tag
     * @return Tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get Tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set objectType
     *
     * @param ObjectTypeType $objectType
     * @return Tag
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * Get objectType
     *
     * @return ObjectTypeType 
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     * @return Tag
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
     * Set slug
     *
     * @param string $slug
     * @return Tag
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
     * Add tags
     *
     * @param \LocalsBest\CommonBundle\Entity\Tag $tags
     * @return Tag
     */
    public function addTag(\LocalsBest\CommonBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

}
