<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PaidInvite
 *
 * @ORM\Table(name="paid_invites")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PaidInviteRepository")
 */
class PaidInvite
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
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(name="recipient", type="string", length=64, nullable=false)
     */
    private $recipient;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="phone_number", type="string", length=32, nullable=true)
     */
    private $phone_number;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="business_name", type="string", length=64, nullable=true)
     */
    private $business_name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="contact_name", type="string", length=128, nullable=true)
     */
    private $contact_name;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="IndustryType")
     * @ORM\JoinColumn(name="industry_type_id", referencedColumnName="id")
     */
    private $industryType;

    /**
     * @ORM\Column(name="token", type="string", length=64, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id")
     */
    private $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_for_paid_invite")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Payment", inversedBy="paid_invite")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;



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
     * Set token
     *
     * @param string $token
     * @return PaidInvite
     */
    public function setToken($token)
    {
        $this->token = $token;

        $this->setCreatedAt(new \DateTime('now'));

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return PaidInvite
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set industryType
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industryType
     * @return PaidInvite
     */
    public function setIndustryType(\LocalsBest\UserBundle\Entity\IndustryType $industryType = null)
    {
        $this->industryType = $industryType;

        return $this;
    }

    /**
     * Get industryType
     *
     * @return \LocalsBest\UserBundle\Entity\IndustryType 
     */
    public function getIndustryType()
    {
        return $this->industryType;
    }

    /**
     * Set created_by
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return PaidInvite
     */
    public function setCreatedBy(\LocalsBest\UserBundle\Entity\User $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set recipient
     *
     * @param string $recipient
     * @return PaidInvite
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return string 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return PaidInvite
     */
    public function setUser(\LocalsBest\UserBundle\Entity\User $user = null)
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
     * Set payment
     *
     * @param \LocalsBest\UserBundle\Entity\Payment $payment
     * @return PaidInvite
     */
    public function setPayment(\LocalsBest\UserBundle\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \LocalsBest\UserBundle\Entity\Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @return mixed
     */
    public function getBusinessName()
    {
        return $this->business_name;
    }

    /**
     * @param mixed $business_name
     */
    public function setBusinessName($business_name)
    {
        $this->business_name = $business_name;
    }

    /**
     * @return mixed
     */
    public function getContactName()
    {
        return $this->contact_name;
    }

    /**
     * @param mixed $contact_name
     */
    public function setContactName($contact_name)
    {
        $this->contact_name = $contact_name;
    }
}
