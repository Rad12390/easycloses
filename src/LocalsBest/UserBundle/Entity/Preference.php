<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * LocalsBest\UserBundle\Entity\Preference
 *
 * @ORM\Table(name="preference")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PreferenceRepository")
 * 
 */
class Preference 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="boolean")
     * 
     */
    protected $sms = false;
    
    /**
     * @ORM\Column(type="boolean")
     * 
     */
    protected $mail = true;
    
    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\User", mappedBy="preference")
     */
    protected $user;
    
    

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
     * Set sms
     *
     * @param boolean $sms
     * @return Preference
     */
    public function setSms($sms)
    {
        $this->sms = $sms;

        return $this;
    }

    /**
     * Get sms
     *
     * @return boolean 
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * Set mail
     *
     * @param boolean $mail
     * @return Preference
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return boolean 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return Preference
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
}
