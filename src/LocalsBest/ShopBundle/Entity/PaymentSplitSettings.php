<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PaymentSplitSettings
 *
 * @ORM\Table(name="payments_split_settings")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\PaymentSplitSettingsRepository")
 */
class PaymentSplitSettings {

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
     * @ORM\Column(name="target", type="string", length=32)
     */
    private $target = 'user';

    /**
     * @var string
     *
     * @ORM\Column(name="targettwo", type="string", length=32)
     */
    private $targettwo = 'user';

    /**
     * @var integer
     *
     * @ORM\Column(name="charity_percentage", type="integer")
     */
    private $charityPercentage = 0;



    /**
     * @var integer
     *
     * @ORM\Column(name="manager_employee_percentage", type="integer")
     */
    private $managerEmployeePercentage = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="business_percentage", type="integer")
     */
    private $businessPercentage = 0;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rebateStatus", type="string", length=32)
     */
    private $rebateStatus = 'charity';

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="paymentSplitSettings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\Business")
     * @ORM\JoinTable(name="users_charities",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="charity_id", referencedColumnName="id")}
     *      )
     */
    private $charities;


    /**
     * Constructor
     */
    public function __construct() {
        $this->charities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return PaymentSplitSettings
     */
    public function setTarget($target) {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Set target
     *
     * @param string $targettwo
     * @return PaymentSplitSettings
     */
    public function setTargetTwo($targettwo) {
        $this->targettwo = $targettwo;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTargetTwo() {
        return $this->targettwo;
    }
    

    /**
     * Set charityPercentage
     *
     * @param integer $charityPercentage
     * @return PaymentSplitSettings
     */
    public function setCharityPercentage($charityPercentage) {
        $this->charityPercentage = $charityPercentage;

        return $this;
    }

    /**
     * Get charityPercentage
     *
     * @return integer 
     */
    public function getCharityPercentage() {
        return $this->charityPercentage;
    }

   
    /**
     * Set managerEmployeePercentage
     *
     * @param integer $managerEmployeePercentage
     * @return PaymentSplitSettings
     */
    public function setManagerEmployeePercentage($managerEmployeePercentage) {
        $this->managerEmployeePercentage = $managerEmployeePercentage;

        return $this;
    }

    /**
     * Get managerEmployeePercentage
     *
     * @return integer 
     */
    public function getManagerEmployeePercentage() {
        return $this->managerEmployeePercentage;
    }

    /**
     * Set businessPercentage
     *
     * @param integer $businessPercentage
     * @return PaymentSplitSettings
     */
    public function setBusinessPercentage($businessPercentage) {
        $this->businessPercentage = $businessPercentage;

        return $this;
    }

    /**
     * Get businessPercentage
     *
     * @return integer 
     */
    public function getBusinessPercentage() {
        return $this->businessPercentage;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return PaymentSplitSettings
     */
    public function setUser(User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add charities
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return PaymentSplitSettings
     */
    public function addCharity(\LocalsBest\UserBundle\Entity\Business $business) {
        $this->charities[] = $business;

        return $this;
    }

    /**
     * Remove charities
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     */
    public function removeCharity(\LocalsBest\UserBundle\Entity\Business $business) {
        $this->charities->removeElement($business);
    }

    /**
     * Get charities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCharities() {
        return $this->charities;
    }


    /**
     * Remove charities
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     */
    public function removeManagerCharity(\LocalsBest\UserBundle\Entity\Business $business) {
        $this->managerCharities->removeElement($business);
    }

    /**
     * @Assert\Callback
     */
    public function checkPercents(ExecutionContext $context) { 
//        if ($this->charityPercentage + $this->employeePercentage > 100) {
//            $context->addViolation('Charity and Employee Percentage can not be greater then 100', [], null);
//        }
    }
    
    /**
     * Set target
     *
     * @param string $rebateStatus
     * @return PaymentSplitSettings
     */
    public function setRebateStatus($rebateStatus) {
        $this->rebateStatus = $rebateStatus;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getRebateStatus() {
        return $this->rebateStatus;
    }
}
