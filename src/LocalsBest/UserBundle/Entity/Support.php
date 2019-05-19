<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * LocalsBest\UserBundle\Entity\Support
 *
 * @ORM\Table(name="supports")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\SupportRepository")
 */
class Support
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The note text
     * @var string
     * @ORM\Column( type="text", nullable=true)
     */
    protected $note;
    
    /**
     * @ORM\Column(nullable=true)
     */
    protected $type;
    
    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Timestampable(on="create")
     * @var integer
     */
    protected $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User")
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $createdBy;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business")
     * @var \LocalsBest\UserBundle\Entity\Business 
     */
    protected $owner;
    

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
     * Set note
     *
     * @param string $note
     * @return Support
     */
    public function setNote($note)
    {
        $this->note = $note;

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
     * Set type
     *
     * @param string $type
     * @return Support
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
     * Set created
     *
     * @param integer $created
     * @return \LocalsBest\UserBundle\Entity\Support
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
     * Set owner
     *
     * @param \LocalsBest\UserBundle\Entity\Business $owner
     * @return \LocalsBest\UserBundle\Entity\Support
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
     * Set Created By
     * 
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return \LocalsBest\UserBundle\Entity\Support
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
}
