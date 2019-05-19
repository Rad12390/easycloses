<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;



/**
 * LocalsBest\UserBundle\Entity\Profile
 *
 * @ORM\Table(name="profiles")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ProfileRepository")
 * @Vich\Uploadable
 */
class Profile
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    
    
    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $aboutMe;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $dba;
    
    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Language")
     * 
     */
    protected $languages;
    
    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\City")
     * 
     */
    protected $workingCities;
    
    /**
     * @Vich\UploadableField(mapping="users", fileNameProperty="fileName")
     *
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File $file
     */
    protected $file;
    
    /**
     * @ORM\Column(type="string", length=255, name="file_name", nullable=true )
     * 
     */
    protected $fileName;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->workingCities = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->setAboutMe('No Bio entered yet');
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
     * Set dba
     *
     * @param string $dba
     * @return Profile
     */
    public function setDba($dba)
    {
        $this->dba = $dba;

        return $this;
    }

    /**
     * Get dba
     *
     * @return string 
     */
    public function getDba()
    {
        return $this->dba;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return Profile
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
     * Add languages
     *
     * @param \LocalsBest\UserBundle\Entity\Language $languages
     * @return Profile
     */
    public function addLanguage(\LocalsBest\UserBundle\Entity\Language $languages)
    {
        $this->languages[] = $languages;

        return $this;
    }

    /**
     * Remove languages
     *
     * @param \LocalsBest\UserBundle\Entity\Language $languages
     */
    public function removeLanguage(\LocalsBest\UserBundle\Entity\Language $languages)
    {
        $this->languages->removeElement($languages);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Add workingCities
     *
     * @param \LocalsBest\UserBundle\Entity\City $workingCities
     * @return Profile
     */
    public function addWorkingCity(\LocalsBest\UserBundle\Entity\City $workingCities)
    {
        $this->workingCities[] = $workingCities;

        return $this;
    }

    /**
     * Remove workingCities
     *
     * @param \LocalsBest\UserBundle\Entity\City $workingCities
     */
    public function removeWorkingCity(\LocalsBest\UserBundle\Entity\City $workingCities)
    {
        $this->workingCities->removeElement($workingCities);
    }

    /**
     * Get workingCities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWorkingCities()
    {
        return $this->workingCities;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return Profile
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string 
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Profile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }
    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile($file = null)
    {
        if (!$file instanceof \Symfony\Component\HttpFoundation\File\File) {
            return $this;
        }

        $this->file = $file;

//        $this->fileName = $file->getFilename();
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}
