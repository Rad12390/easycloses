<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * LocalsBest\UserBundle\Entity\State
 *
 * @ORM\Table(name="states")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\StateRepository")
 */
class State
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $short_name;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\City", mappedBy="state")
     * @var ArrayCollection
     */
    protected $cities;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="forState")
     */
    private $productAble;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\ClientBusiness", mappedBy="states")
     * @ORM\JoinTable(name="clientBusinesses_states")
     */
    private $client_businesses;


    public function __construct()
    {
        $this->productAble = new ArrayCollection();
        $this->client_businesses = new ArrayCollection();
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
     * Set name
     * @param string $name
     * @return State
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * @param mixed $short_name
     */
    public function setShortName($short_name)
    {
        $this->short_name = $short_name;
    }

    public function __toString()
    {
        return $this->getShortName();
    }

    /**
     * Add cities
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     * @return State
     */
    public function addCity(City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     */
    public function removeCity(City $cities)
    {
        $this->cities->removeElement($cities);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Add productAble
     *
     * @param \LocalsBest\UserBundle\Entity\Product $productAble
     * @return State
     */
    public function addProductAble(Product $productAble)
    {
        $this->productAble[] = $productAble;

        return $this;
    }

    /**
     * Remove productAble
     *
     * @param \LocalsBest\UserBundle\Entity\Product $productAble
     */
    public function removeProductAble(Product $productAble)
    {
        $this->productAble->removeElement($productAble);
    }

    /**
     * Get productAble
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductAble()
    {
        return $this->productAble;
    }

    /**
     * Add client_businesses
     *
     * @param \LocalsBest\UserBundle\Entity\ClientBusiness $clientBusinesses
     * @return State
     */
    public function addClientBusiness(ClientBusiness $clientBusinesses)
    {
        $this->client_businesses[] = $clientBusinesses;

        return $this;
    }

    /**
     * Remove client_businesses
     *
     * @param \LocalsBest\UserBundle\Entity\ClientBusiness $clientBusinesses
     */
    public function removeClientBusiness(ClientBusiness $clientBusinesses)
    {
        $this->client_businesses->removeElement($clientBusinesses);
    }

    /**
     * Get client_businesses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClientBusinesses()
    {
        return $this->client_businesses;
    }
}
