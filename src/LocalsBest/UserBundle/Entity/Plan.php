<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Plan
 *
 * @ORM\Table(name="plans")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PlanRepository")
 */
class Plan
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
     * @ORM\Column(name="name", type="string", length=127)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Plan", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\Column(name="is_default_prices", type="boolean", options={"default" = true})
     */
    private $is_default_prices = true;

    /**
     * @ORM\Column(name="is_default_industry_groups", type="boolean", options={"default" = true})
     */
    private $is_default_industry_groups = true;

    /**
     * @ORM\OneToMany(targetEntity="PlanRow", mappedBy="plan", cascade={"persist"})
     */
    private $rows;

    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", mappedBy="plan")
     */
    protected $business;


    public function __construct() {
        $this->rows     = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function __clone()
    {

        if ($this->id) {
            $this->setId(null);
        }

        $indGroups = $this->getRows();
        $this->pupils = new ArrayCollection();
        if(null !== $indGroups) {
            foreach ($indGroups as $indGroup) {
                $cloneIndGroup = clone $indGroup;
                $this->addRow($cloneIndGroup);
                $cloneIndGroup->setPlan($this);
            }
        }
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

    public function setId($value)
    {
        return $this->id = $value;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Plan
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
     * Add rows
     *
     * @param \LocalsBest\UserBundle\Entity\PlanRow $rows
     * @return Plan
     */
    public function addRow(PlanRow $rows)
    {
        $this->rows[] = $rows;

        return $this;
    }

    /**
     * Remove rows
     *
     * @param \LocalsBest\UserBundle\Entity\PlanRow $rows
     */
    public function removeRow(PlanRow $rows)
    {
        $this->rows->removeElement($rows);
    }

    /**
     * Get rows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Add children
     *
     * @param \LocalsBest\UserBundle\Entity\Plan $children
     * @return Plan
     */
    public function addChild(Plan $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \LocalsBest\UserBundle\Entity\Plan $children
     */
    public function removeChild(Plan $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \LocalsBest\UserBundle\Entity\Plan $parent
     * @return Plan
     */
    public function setParent(Plan $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \LocalsBest\UserBundle\Entity\Plan 
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return Plan
     */
    public function setBusiness(Business $business = null)
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
     * Set is_default_prices
     *
     * @param boolean $isDefaultPrices
     * @return Plan
     */
    public function setIsDefaultPrices($isDefaultPrices)
    {
        $this->is_default_prices = $isDefaultPrices;

        return $this;
    }

    /**
     * Get is_default_prices
     *
     * @return boolean 
     */
    public function isDefaultPrices()
    {
        return $this->is_default_prices;
    }

    /**
     * Set is_default_industry_groups
     *
     * @param boolean $isDefaultIndustryGroups
     * @return Plan
     */
    public function setIsDefaultIndustryGroups($isDefaultIndustryGroups)
    {
        $this->is_default_industry_groups = $isDefaultIndustryGroups;

        return $this;
    }

    /**
     * Get is_default_industry_groups
     *
     * @return boolean 
     */
    public function isDefaultIndustryGroups()
    {
        return $this->is_default_industry_groups;
    }
}
