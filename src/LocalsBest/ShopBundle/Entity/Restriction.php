<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LocalsBest\UserBundle\Entity\Role;
use LocalsBest\UserBundle\Entity\State;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\Business;

/**
 * Restriction
 *
 * @ORM\Table(name="shop_restrictions")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\RestrictionRepository")
 */
class Restriction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="roles_switch", type="string", length=16, nullable=true)
     */
    private $rolesSwitch;

    /**
     * Many Restrictions have Many Roles.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Role", inversedBy="shopRestrictions")
     * @ORM\JoinTable(name="restrictions_roles")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="states_switch", type="string", length=16, nullable=true)
     */
    private $statesSwitch;

    /**
     * Many Restrictions have Many States.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\State", inversedBy="shopRestrictions")
     * @ORM\JoinTable(name="restrictions_states")
     */
    private $states;

    /**
     * @var string
     *
     * @ORM\Column(name="cities_switch", type="string", length=16, nullable=true)
     */
    private $citiesSwitch;

    /**
     * Many Restrictions have Many Cities.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\City", inversedBy="shopRestrictions")
     * @ORM\JoinTable(name="restrictions_cities")
     */
    private $cities;

    /**
     * @var string
     *
     * @ORM\Column(name="industries_switch", type="string", length=16, nullable=true)
     */
    private $industriesSwitch;

    /**
     * Many Restrictions have Many Industries.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\IndustryType", inversedBy="shopRestrictions")
     * @ORM\JoinTable(name="industries_restrictions")
     */
    private $industries;

    /**
     * @var string
     *
     * @ORM\Column(name="businesses_switch", type="string", length=16, nullable=true)
     */
    private $businessesSwitch;

    /**
     * Many Restrictions have Many Businesses.
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="shopRestrictions")
     * @ORM\JoinTable(name="businesses_restrictions")
     */
    private $businesses;

    /**
     * @ORM\ManyToMany(targetEntity="Sku", inversedBy="restrictions")
     * @ORM\JoinTable(name="restrictions_skus")
     */
    private $skus;

    /**
     * @ORM\ManyToMany(targetEntity="Sku", inversedBy="adminRestrictions")
     * @ORM\JoinTable(name="admin_restrictions_skus")
     */
    private $adminSkus;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="restrictions")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Package", inversedBy="restrictions")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $package;
    
    /**
     * @var string
     *
     * @ORM\Column(name="buyerType", type="string", length=16, nullable=true)
     */
    private $buyerType;


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
     * Set type
     *
     * @param string $type
     * @return Restriction
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
     * Set rolesSwitch
     *
     * @param string $rolesSwitch
     * @return Restriction
     */
    public function setRolesSwitch($rolesSwitch)
    {
        $this->rolesSwitch = $rolesSwitch;

        return $this;
    }

    /**
     * Get rolesSwitch
     *
     * @return string
     */
    public function getRolesSwitch()
    {
        return $this->rolesSwitch;
    }

    /**
     * Set statesSwitch
     *
     * @param string $statesSwitch
     * @return Restriction
     */
    public function setStatesSwitch($statesSwitch)
    {
        $this->statesSwitch = $statesSwitch;

        return $this;
    }

    /**
     * Get statesSwitch
     *
     * @return string
     */
    public function getStatesSwitch()
    {
        return $this->statesSwitch;
    }
    /**
     * Set buyerType
     *
     * @param string $buyerType
     * @return Restriction
     */
    public function setBuyerType($buyerType)
    {
        $this->buyerType = $buyerType;

        return $this;
    }

    /**
     * Get buyerType
     *
     * @return string
     */
    public function getBuyerType()
    {
        return $this->buyerType;
    }

    /**
     * Set industriesSwitch
     *
     * @param string $industriesSwitch
     * @return Restriction
     */
    public function setIndustriesSwitch($industriesSwitch)
    {
        $this->industriesSwitch = $industriesSwitch;

        return $this;
    }

    /**
     * Get industriesSwitch
     *
     * @return string
     */
    public function getIndustriesSwitch()
    {
        return $this->industriesSwitch;
    }

    /**
     * Set businessesSwitch
     *
     * @param string $businessesSwitch
     * @return Restriction
     */
    public function setBusinessesSwitch($businessesSwitch)
    {
        $this->businessesSwitch = $businessesSwitch;

        return $this;
    }

    /**
     * Get businessesSwitch
     *
     * @return string
     */
    public function getBusinessesSwitch()
    {
        return $this->businessesSwitch;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->states = new ArrayCollection();
        $this->industries = new ArrayCollection();
        $this->businesses = new ArrayCollection();
        $this->skus = new ArrayCollection();
        $this->adminSkus = new ArrayCollection();
    }


    /**
     * Add roles
     *
     * @param \LocalsBest\UserBundle\Entity\Role $roles
     * @return Restriction
     */
    public function addRole(Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }
    /**
     * Add roles
     *
     * @param \LocalsBest\UserBundle\Entity\Role $roles
     * @return Restriction
     */
    public function setRole($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \LocalsBest\UserBundle\Entity\Role $roles
     */
    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add states
     *
     * @param \LocalsBest\UserBundle\Entity\State $states
     * @return Restriction
     */
    public function addState(State $states)
    {
        $this->states[] = $states;

        return $this;
    }
    
    /**
     * Add states
     *
     * @param \LocalsBest\UserBundle\Entity\State $states
     * @return Restriction
     */
    public function setState($states)
    {
        $this->states = $states;

        return $this;
    }

    /**
     * Remove states
     *
     * @param \LocalsBest\UserBundle\Entity\State $states
     */
    public function removeState(State $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Add industries
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industries
     * @return Restriction
     */
    public function addIndustry(IndustryType $industries)
    {
        $this->industries[] = $industries;

        return $this;
    }
    /**
     * Set industries
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industries
     * @return Restriction
     */
    public function setIndustry($industries)
    {
        $this->industries = $industries;

        return $this;
    }

    /**
     * Remove industries
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industries
     */
    public function removeIndustry(IndustryType $industries)
    {
        $this->industries->removeElement($industries);
    }

    /**
     * Get industries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndustries()
    {
        return $this->industries;
    }

    /**
     * Add businesses
     *
     * @param \LocalsBest\UserBundle\Entity\Business $businesses
     * @return Restriction
     */
    public function addBusiness(Business $businesses)
    {
        $this->businesses[] = $businesses;

        return $this;
    }
    /**
     * Set businesses
     *
     * @param \LocalsBest\UserBundle\Entity\Business $businesses
     * @return Restriction
     */
    public function setBusiness($businesses)
    {
        $this->businesses = $businesses;

        return $this;
    }

    /**
     * Remove businesses
     *
     * @param \LocalsBest\UserBundle\Entity\Business $businesses
     */
    public function removeBusiness(Business $businesses)
    {
        $this->businesses->removeElement($businesses);
    }

    /**
     * Get businesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinesses()
    {
        return $this->businesses;
    }

    /**
     * Add SKU
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return Restriction
     */
    public function addSku(Sku $sku)
    {
        $this->skus[] = $sku;

        return $this;
    }

    /**
     * Remove sku
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     */
    public function removeSku(Sku $sku)
    {
        $this->skus->removeElement($sku);
    }

    /**
     * Get SKUs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkus()
    {
        return $this->skus;
    }

    /**
     * Set item
     *
     * @param \LocalsBest\ShopBundle\Entity\Item $item
     * @return Restriction
     */
    public function setItem(Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \LocalsBest\ShopBundle\Entity\Item 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set package
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $package
     * @return Restriction
     */
    public function setPackage(Package $package = null)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return \LocalsBest\ShopBundle\Entity\Package 
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set citiesSwitch
     *
     * @param string $citiesSwitch
     * @return Restriction
     */
    public function setCitiesSwitch($citiesSwitch)
    {
        $this->citiesSwitch = $citiesSwitch;

        return $this;
    }

    /**
     * Get citiesSwitch
     *
     * @return string
     */
    public function getCitiesSwitch()
    {
        return $this->citiesSwitch;
    }

    /**
     * Add cities
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     * @return Restriction
     */
    public function addCity(\LocalsBest\UserBundle\Entity\City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }
    
        /**
     * Add states
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     * @return Restriction
     */
    public function setCity($cities)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     */
    public function removeCity(\LocalsBest\UserBundle\Entity\City $cities)
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
     * Add skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     * @return Restriction
     */
    public function addSkus(\LocalsBest\ShopBundle\Entity\Sku $skus)
    {
        $this->skus[] = $skus;

        return $this;
    }

    /**
     * Remove skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     */
    public function removeSkus(\LocalsBest\ShopBundle\Entity\Sku $skus)
    {
        $this->skus->removeElement($skus);
    }

    /**
     * Add adminSkus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $adminSkus
     * @return Restriction
     */
    public function addAdminSkus(\LocalsBest\ShopBundle\Entity\Sku $adminSkus)
    {
        $this->adminSkus[] = $adminSkus;

        return $this;
    }

    /**
     * Remove adminSkus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $adminSkus
     */
    public function removeAdminSkus(\LocalsBest\ShopBundle\Entity\Sku $adminSkus)
    {
        $this->adminSkus->removeElement($adminSkus);
    }

    /**
     * Get adminSkus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdminSkus()
    {
        return $this->adminSkus;
    }
}
