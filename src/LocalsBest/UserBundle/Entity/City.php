<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * LocalsBest\UserBundle\Entity\City
 * @ORM\Table(name="cities")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\CityRepository")
 */
class City
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\State", inversedBy="cities", cascade={"all"})
     * @var State
     */
    protected $state;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\ClientBusiness", mappedBy="cities")
     * @ORM\JoinTable(name="clientBusinesses_cities")
     */
    protected $client_businesses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client_businesses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     * @param string $name
     * @return City
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
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Add client_businesses
     *
     * @param \LocalsBest\UserBundle\Entity\ClientBusiness $clientBusinesses
     * @return City
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
