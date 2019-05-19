<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentSet
 *
 * @ORM\Table(name="payment_sets")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PaymentSetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PaymentSet
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
     * @ORM\Column(name="amount", type="decimal")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="paymentSets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PaymentRow", mappedBy="paymentSet")
     */
    private $paymentRows;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paymentRows = new ArrayCollection();
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
     * Set amount
     *
     * @param string $amount
     * @return PaymentSet
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PaymentSet
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return PaymentSet
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Add paymentRows
     *
     * @param \LocalsBest\UserBundle\Entity\PaymentRow $paymentRows
     * @return PaymentSet
     */
    public function addPaymentRow(PaymentRow $paymentRows)
    {
        $this->paymentRows[] = $paymentRows;

        return $this;
    }

    /**
     * Remove paymentRows
     *
     * @param \LocalsBest\UserBundle\Entity\PaymentRow $paymentRows
     */
    public function removePaymentRow(PaymentRow $paymentRows)
    {
        $this->paymentRows->removeElement($paymentRows);
    }

    /**
     * Get paymentRows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaymentRows()
    {
        return $this->paymentRows;
    }

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}
