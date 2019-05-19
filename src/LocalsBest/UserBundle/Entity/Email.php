<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * LocalsBest\UserBundle\Entity\Email
 *
 * @ORM\Table(name="emails", uniqueConstraints={@ORM\UniqueConstraint(name="unique_email", columns={"email", "contact_id"})})
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\EmailRepository")
 * @UniqueEntity(fields={"email", "contact"}, message="Email id entered is already registered. Please try a new email id")
 */
class Email 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="email")
     * @Assert\NotBlank()
     * @Assert\Email
     */
    protected $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Contact", inversedBy="emails")
     * @var type 
     */
    protected $contact;
    
    public function __construct($email = null)
    {
        if ($email) {
            $this->email = $email;
        }
    }
    
    public function __toString()
    {
        return $this->email;
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
     * @return Email
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
     * Set contact
     *
     * @param \LocalsBest\UserBundle\Entity\Contact $contact
     * @return Email
     */
    public function setContact(\LocalsBest\UserBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \LocalsBest\UserBundle\Entity\Contact 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return Email
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
    }
