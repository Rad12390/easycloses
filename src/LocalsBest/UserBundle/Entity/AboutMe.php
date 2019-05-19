<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * LocalsBest\UserBundle\Entity\AboutMe
 *
 * @ORM\Table(name="aboutmes")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\AboutMeMeRepository")
 */
class AboutMe
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
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="aboutMe")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     * @var \LocalsBest\UserBundle\Entity\Business
     */
    protected $business;
    
    public function __construct($aboutMe = null)
    {
        $this->aboutMe = $aboutMe;
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
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return AboutMe
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
     * Set Business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return AboutMe
     */
    public function setBusiness(Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get Business
     *
     * @return \LocalsBest\UserBundle\Entity\Business 
     */
    public function getBusiness()
    {
        return $this->business;
    }
}
