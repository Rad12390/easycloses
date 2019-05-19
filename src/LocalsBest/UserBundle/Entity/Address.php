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
 * LocalsBest\UserBundle\Entity\Address
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\AddressRepository")
 */
class Address
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable= true)
     */
    protected $street;
    
    /**
     * @ORM\Column(type="string", length=100, nullable= true)
     */
    protected $state;
    
    /**
     * @ORM\Column(type="string", length=100, nullable= true)
     */
    protected $city;
    
    /**
     * @Assert\Regex(
     *     pattern="/\d{5}/",
     *     match=true,
     *     message="You set wrong ZIP code"
     * )
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $zip;
    
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
     * Set zip
     *
     * @param integer $zip
     * @return Address
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return integer 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set property
     *
     * @param \LocalsBest\UserBundle\Entity\Property $property
     * @return Address
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \LocalsBest\UserBundle\Entity\Property 
     */
    public function getProperty()
    {
        return $this->property;
    }


    /**
     * Set street
     *
     * @param string $street
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Address
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    public function getFull()
    {
        return $this->street .
            ', ' . $this->city .
            ', ' . $this->state .
            ', ' . $this->zip
        ;
    }
}
