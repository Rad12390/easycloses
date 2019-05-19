<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CouponUse
 *
 * @ORM\Table(name="shop_coupon_uses")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\CouponUseRepository")
 */
class CouponUse
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
     * @var Payment
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\ShopBundle\Entity\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @var Coupon
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Coupon")
     * @ORM\JoinColumn(name="coupon_id", referencedColumnName="id")
     */
    private $coupon;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;


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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CouponUse
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
     * Set payment
     *
     * @param \LocalsBest\ShopBundle\Entity\Payment $payment
     * @return CouponUse
     */
    public function setPayment(Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \LocalsBest\ShopBundle\Entity\Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set coupon
     *
     * @param \LocalsBest\ShopBundle\Entity\Coupon $coupon
     * @return CouponUse
     */
    public function setCoupon(Coupon $coupon = null)
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Get coupon
     *
     * @return \LocalsBest\ShopBundle\Entity\Coupon 
     */
    public function getCoupon()
    {
        return $this->coupon;
    }
}
