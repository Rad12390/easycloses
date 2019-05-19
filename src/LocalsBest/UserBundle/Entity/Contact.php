<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * LocalsBest\UserBundle\Entity\Contact
 *
 * @ORM\Table(name="contacts")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ContactRepository")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 * 
 */
class Contact
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\User", mappedBy="contact", cascade={"all"})
     * @var type 
     */
    protected $user;
    
    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", mappedBy="contact", cascade={"all"})
     * @var type 
     */
    protected $business;
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Email", mappedBy="contact", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $emails;
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Phone", mappedBy="contact",  cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $phones;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var integer
     */
    protected $deleted;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Contact
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
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return Contact
     */
    public function setBusiness(\LocalsBest\UserBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \LocalsBest\UserBundle\Entity\Business 
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Add emails
     *
     * @param \LocalsBest\UserBundle\Entity\Email $emails
     * @return Contact
     */
    public function addEmail(\LocalsBest\UserBundle\Entity\Email $emails)
    {
        $this->emails[] = $emails;

        if ($this->emails->count() === 1 && $this->getUser()) {
            $this->getUser()->setPrimaryEmail($emails);
        }

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \LocalsBest\UserBundle\Entity\Email $emails
     */
    public function removeEmail(\LocalsBest\UserBundle\Entity\Email $emails)
    {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Add phones
     *
     * @param \LocalsBest\UserBundle\Entity\Phone $phones
     * @return Contact
     */
    public function addPhone(\LocalsBest\UserBundle\Entity\Phone $phones)
    {
        $this->phones[] = $phones;

        if ($this->phones->count() === 1 && $this->getUser()) {
            $this->getUser()->setPrimaryPhone($phones);
        }

        return $this;
    }

    /**
     * Remove phones
     *
     * @param \LocalsBest\UserBundle\Entity\Phone $phones
     */
    public function removePhone(\LocalsBest\UserBundle\Entity\Phone $phones)
    {
        $this->phones->removeElement($phones);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhones()
    {
        return $this->phones;
    }

}
