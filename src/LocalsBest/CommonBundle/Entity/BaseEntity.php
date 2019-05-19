<?php

namespace LocalsBest\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of Object
 *
 * @author abhinav
 * @ORM\MappedSuperclass
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 */
class BaseEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Gedmo\Slug(fields={"name", "id"}, updatable=true, unique=true)
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\CommonBundle\Entity\Status")
     * @Assert\NotBlank()
     * @var \LocalsBest\CommonBundle\Entity\Status
     */
    protected $status;
    
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
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", cascade="persist")
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $createdBy;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", cascade="persist")
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $updatedBy;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", cascade="persist")
     * @var \LocalsBest\UserBundle\Entity\Business 
     */
    protected $owner;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="\LocalsBest\CommonBundle\Entity\Note", cascade={"all"})
     * @ORM\OrderBy({"created" = "DESC"})
     *
     */
    protected $notes;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="\LocalsBest\CommonBundle\Entity\Tag", cascade={"all"})
     */
    protected $tags;
    
    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Share", cascade={"all"})
     * 
     **/
   protected $shares;


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
     * Gets the slug
     * @return string
     * 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return \LocalsBest\CommonBundle\Entity\Object
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
    
    /**
     * 
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set status
     *
     * @param Status $status
     * @return \LocalsBest\CommonBundle\Entity\BaseEntity
     */
    public function setStatus(\LocalsBest\CommonBundle\Entity\Status $status)
    {
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * Set created
     *
     * @param integer $created
     * @return \LocalsBest\CommonBundle\Entity\BaseEntity
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
     * @return \LocalsBest\CommonBundle\Entity\Object
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
     * Set owner
     *
     * @param \LocalsBest\UserBundle\Entity\Business $owner
     * @return \LocalsBest\CommonBundle\Entity\BaseEntity
     */
    public function setOwner(\LocalsBest\UserBundle\Entity\Business $owner = null)
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
    
    /**
     * Add notes
     *
     * @param \LocalsBest\CommonBundle\Entity\Note $notes
     * @return \LocalsBest\CommonBundle\Entity\Object
     */
    public function addNote(\LocalsBest\CommonBundle\Entity\Note $note)
    {
        $this->notes[] = $note;
        
        return $this;
    }
    
    /**
     * Remove notes
     *
     * @param \LocalsBest\CommonBundle\Entity\Note $notes
     */
    public function removeNote(\LocalsBest\CommonBundle\Entity\Note $note)
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
     * Set Created By
     * 
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return BaseEntity
     */
    public function setCreatedBy(\LocalsBest\UserBundle\Entity\User $user)
    {
        $this->createdBy = $user;
        
        return $this;
    }
    
    /**
     * Get Created By
     * 
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    
    /**
     * Add shares
     *
     * @param \LocalsBest\UserBundle\Entity\Share $shares
     * @return \LocalsBest\CommonBundle\Entity\Object
     */
    public function addShare(\LocalsBest\UserBundle\Entity\Share $shares)
    {
        $this->shares[] = $shares;

        return $this;
    }

    /**
     * Remove shares
     *
     * @param \LocalsBest\UserBundle\Entity\Share $shares
     */
    public function removeShare(\LocalsBest\UserBundle\Entity\Share $shares)
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
    
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
        
        return $this;
    }
    
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
        
        return $this;
    }
    
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }
}
