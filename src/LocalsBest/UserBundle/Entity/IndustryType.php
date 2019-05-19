<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LocalsBest\ShopBundle\Entity\Package;


/**
 * LocalsBest\UserBundle\Entity\IndustryType
 *
 * @ORM\Table(name="industryTypes")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\IndustryTypeRepository")
 */
class IndustryType
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
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Business", mappedBy="types")
     */
    protected $businesses;

    /**
     * @ORM\ManyToMany(targetEntity="PlanRow", mappedBy="industry_type")
     */
    protected $plan_row;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="forIndustry")
     */
    private $productAble;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\ClientBusiness", mappedBy="industry_type")
     */
    private $client_businesses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Package", mappedBy="industryType")
     */
    private $packages;

    public function __construct() {
        $this->plan_row = new ArrayCollection();
        $this->businesses = new ArrayCollection();
        $this->productAble = new ArrayCollection();
        $this->client_businesses = new ArrayCollection();
        $this->packages = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     *
     * @param string $name
     * @return IndustryType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add businesses
     *
     * @param \LocalsBest\UserBundle\Entity\Business $businesses
     * @return IndustryType
     */
    public function addBusiness(Business $businesses)
    {
        $this->businesses[] = $businesses;

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
     * Add plan_row
     *
     * @param \LocalsBest\UserBundle\Entity\PlanRow $planRow
     * @return IndustryType
     */
    public function addPlanRow(PlanRow $planRow)
    {
        $this->plan_row[] = $planRow;

        return $this;
    }

    /**
     * Remove plan_row
     *
     * @param \LocalsBest\UserBundle\Entity\PlanRow $planRow
     */
    public function removePlanRow(PlanRow $planRow)
    {
        $this->plan_row->removeElement($planRow);
    }

    /**
     * Get plan_row
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlanRow()
    {
        return $this->plan_row;
    }

    /**
     * Add productAble
     *
     * @param \LocalsBest\UserBundle\Entity\Product $productAble
     * @return IndustryType
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
     * @return IndustryType
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


    /**
     * Add packages
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $packages
     * @return IndustryType
     */
    public function addPackage(Package $packages)
    {
        $this->packages[] = $packages;

        return $this;
    }

    /**
     * Remove packages
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $packages
     */
    public function removePackage(Package $packages)
    {
        $this->packages->removeElement($packages);
    }

    /**
     * Get packages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPackages()
    {
        return $this->packages;
    }
}
