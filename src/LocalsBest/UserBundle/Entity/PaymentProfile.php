<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentProfile
 *
 * @ORM\Table(name="payment_profiles")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PaymentProfileRepository")
 */
class PaymentProfile
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
     * @ORM\Column(name="payment_profile_id", type="string", length=32)
     */
    private $paymentProfileId;

    /**
     * @var integer
     *
     * @ORM\Column(name="last4", type="integer")
     */
    private $last4;

    /**
     * @var string
     *
     * @ORM\Column(name="expiration_date", type="string", length=16)
     */
    private $expirationDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="paymentProfiles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


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
     * Set paymentProfileId
     *
     * @param string $paymentProfileId
     * @return PaymentProfile
     */
    public function setPaymentProfileId($paymentProfileId)
    {
        $this->paymentProfileId = $paymentProfileId;

        return $this;
    }

    /**
     * Get paymentProfileId
     *
     * @return string 
     */
    public function getPaymentProfileId()
    {
        return $this->paymentProfileId;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return PaymentProfile
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set last4
     *
     * @param integer $last4
     * @return PaymentProfile
     */
    public function setLast4($last4)
    {
        $this->last4 = $last4;

        return $this;
    }

    /**
     * Get last4
     *
     * @return integer 
     */
    public function getLast4()
    {
        return $this->last4;
    }

    /**
     * Set expirationDate
     *
     * @param string $expirationDate
     * @return PaymentProfile
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return string 
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
}
