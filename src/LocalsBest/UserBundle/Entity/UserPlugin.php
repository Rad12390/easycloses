<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPlugin
 *
 * @ORM\Table(name="users_plugins_sets")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\UserPluginRepository")
 */
class UserPlugin
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
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var Plugin
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Plugin")
     */
    private $plugin;

    /**
     * @var Plugin
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\Plugin")
     */
    private $payment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="by_admin", type="boolean")
     */
    private $byAdmin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_shared", type="boolean")
     */
    private $isShared;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
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
     * Set byAdmin
     *
     * @param boolean $byAdmin
     * @return UserPlugin
     */
    public function setByAdmin($byAdmin)
    {
        $this->byAdmin = $byAdmin;

        return $this;
    }

    /**
     * Get byAdmin
     *
     * @return boolean 
     */
    public function getByAdmin()
    {
        return $this->byAdmin;
    }

    /**
     * Set isShared
     *
     * @param boolean $isShared
     * @return UserPlugin
     */
    public function setIsShared($isShared)
    {
        $this->isShared = $isShared;

        return $this;
    }

    /**
     * Get isShared
     *
     * @return boolean 
     */
    public function getIsShared()
    {
        return $this->isShared;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserPlugin
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
}
