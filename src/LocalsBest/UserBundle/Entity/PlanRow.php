<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PlanRow
 *
 * @ORM\Table(name="plans_rows")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PlanRowRepository")
 */
class PlanRow
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
     * @ORM\ManyToMany(targetEntity="IndustryType", inversedBy="plan_row")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinTable(name="plan_rows_ind_types",
     *      joinColumns={@ORM\JoinColumn(name="plan_row_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ind_type_id", referencedColumnName="id")}
     *      )
     */
    private $industry_type;

    /**
     * @var integer
     *
     * @ORM\Column(name="industry_group", type="integer", unique=false)
     */
    private $industry_group;

    /**
     * @var float
     *
     * @ORM\Column(name="setup_gold_price", nullable=true, type="decimal", precision=7, scale=2)
     */
    private $setupGoldPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="gold_price", nullable=true, type="decimal", precision=7, scale=2)
     */
    private $goldPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="setup_silver_price", nullable=true, type="decimal", precision=7, scale=2)
     */
    private $setupSilverPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="silver_price", nullable=true, type="decimal", precision=7, scale=2)
     */
    private $silverPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="setup_bronze_price", nullable=true, type="decimal", precision=7, scale=2)
     */
    private $setupBronzePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="bronze_price", nullable=true, type="decimal", precision=7, scale=2)
     */
    private $bronzePrice;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="rows", cascade={"persist"})
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $plan;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->industry_type = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
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
     * Set goldPrice
     *
     * @param string $goldPrice
     * @return PlanRow
     */
    public function setGoldPrice($goldPrice)
    {
        $this->goldPrice = $goldPrice;

        return $this;
    }

    /**
     * Get goldPrice
     *
     * @return int
     */
    public function getGoldPrice()
    {
        return $this->goldPrice;
    }

    /**
     * Set silverPrice
     *
     * @param int $silverPrice
     * @return PlanRow
     */
    public function setSilverPrice($silverPrice)
    {
        $this->silverPrice = $silverPrice;

        return $this;
    }

    /**
     * Get silverPrice
     *
     * @return int
     */
    public function getSilverPrice()
    {
        return $this->silverPrice;
    }

    /**
     * Set bronzePrice
     *
     * @param string $bronzePrice
     * @return PlanRow
     */
    public function setBronzePrice($bronzePrice)
    {
        $this->bronzePrice = $bronzePrice;

        return $this;
    }

    /**
     * Get bronzePrice
     *
     * @return int
     */
    public function getBronzePrice()
    {
        return $this->bronzePrice;
    }

    /**
     * Set plan
     *
     * @param integer $plan
     * @return PlanRow
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return integer 
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set setupGoldPrice
     *
     * @param string $setupGoldPrice
     * @return PlanRow
     */
    public function setSetupGoldPrice($setupGoldPrice)
    {
        $this->setupGoldPrice = $setupGoldPrice;

        return $this;
    }

    /**
     * Get setupGoldPrice
     *
     * @return int
     */
    public function getSetupGoldPrice()
    {
        return $this->setupGoldPrice;
    }

    /**
     * Set setupSilverPrice
     *
     * @param string $setupSilverPrice
     * @return PlanRow
     */
    public function setSetupSilverPrice($setupSilverPrice)
    {
        $this->setupSilverPrice = $setupSilverPrice;

        return $this;
    }

    /**
     * Get setupSilverPrice
     *
     * @return int
     */
    public function getSetupSilverPrice()
    {
        return $this->setupSilverPrice;
    }

    /**
     * Set setupBronzePrice
     *
     * @param string $setupBronzePrice
     * @return PlanRow
     */
    public function setSetupBronzePrice($setupBronzePrice)
    {
        $this->setupBronzePrice = $setupBronzePrice;

        return $this;
    }

    /**
     * Get setupBronzePrice
     *
     * @return int
     */
    public function getSetupBronzePrice()
    {
        return $this->setupBronzePrice;
    }

    /**
     * Set industry_group
     *
     * @param integer $industryGroup
     * @return PlanRow
     */
    public function setIndustryGroup($industry_group)
    {
        $this->industry_group = $industry_group;

        return $this;
    }

    /**
     * Get industry_group
     *
     * @return integer 
     */
    public function getIndustryGroup()
    {
        return $this->industry_group;
    }

    /**
     * Add industry_type
     *
     * @param IndustryType $industryType
     * @return PlanRow
     */
    public function addIndustryType(IndustryType $industryType)
    {
        $this->industry_type[] = $industryType;

        return $this;
    }

    /**
     * Remove industry_type
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industryType
     */
    public function removeIndustryType(IndustryType $industryType)
    {
        $this->industry_type->removeElement($industryType);
    }

    /**
     * Get industry_type
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIndustryType()
    {
        return $this->industry_type;
    }
}
