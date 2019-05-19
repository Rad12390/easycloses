<?php

namespace LocalsBest\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * The Note entity
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="LocalsBest\CommonBundle\Entity\NoteRepository")
 */
class Note extends BaseEntity
{
    /**
     * The note text
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $note;
    
    /**
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
     * @ORM\Column(nullable=true)
     * 
     */
    protected $objectType;
    
    /**
     * @ORM\Column(nullable=true)
     * 
     */
    protected $type;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $important;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $private;
    /**
     * Object ID
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $objectId;
    
    public function __construct($note = null) 
    {
        if(empty($note)) {
            $note = 'Note';
        }
        $this->setNote($note);
        $this->important = false;
        $this->private = false;
    }

    public function __clone()
    {
        $this->id = null;
    }

        /**
     * Set note
     *
     * @param string $note
     * @return Note
     */
    public function setNote($note)
    {
        $this->note = $note;
        
        // replace non letter or digits by -
        $name = preg_replace('~[^\\pL\d]+~u', '-', $note);

        // trim
        $name = trim($name, '-');

        // transliterate
        $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);

        // lowercase
        $name = strtolower($name);

        // remove unwanted characters
        $name = preg_replace('~[^-\w]+~', '', $name);
        
        $slug = substr($name, 0, 15) . "-" . time() . '-' . rand(1, 999999);
        
        $this->slug = $slug;
        
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set objectType
     *
     * @param string $objectType
     * @return Note
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
     * Set type
     *
     * @param string $type
     * @return Note
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
     * Set important
     *
     * @param boolean $important
     * @return Note
     */
    public function setImportant($important)
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Get important
     *
     * @return boolean 
     */
    public function getImportant()
    {
        return $this->important;
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
     * Set private
     *
     * @param boolean $private
     * @return Note
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
    }
    
    /**
     * Set user
     * 
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return \LocalsBest\CommonBundle\Entity\Note
     */
    public function setUser(\LocalsBest\UserBundle\Entity\User $user)
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
}
