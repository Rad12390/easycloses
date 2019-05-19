<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessUserSet
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\BusinessUserSetRepository")
 */
class BusinessUserSet
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Business
     *
     * @ORM\ManyToOne(targetEntity="Business")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * @var integer
     *
     * @ORM\Column(name="quick_book_id", type="integer")
     */
    private $quickBookId;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="text", nullable=true)
     */
    private $roles;


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
     * Set quickBookId
     *
     * @param integer $quickBookId
     * @return BusinessUserSet
     */
    public function setQuickBookId($quickBookId)
    {
        $this->quickBookId = $quickBookId;

        return $this;
    }

    /**
     * Get quickBookId
     *
     * @return integer 
     */
    public function getQuickBookId()
    {
        return $this->quickBookId;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return BusinessUserSet
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return BusinessUserSet
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
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return BusinessUserSet
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
}
