<?php
namespace LocalsBest\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * BusinessView
 *
 * @ORM\Table(name="business_views")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\BusinessViewRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BusinessView
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
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="bio_views")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     * @var Business
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="bio_views")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;
    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text")
     */
    private $info;
    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string")
     */
    private $ip;
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return BusinessView
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
     * Set info
     *
     * @param string $info
     * @return BusinessView
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }
    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }
    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return BusinessView
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
     * @return BusinessView
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
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
    /**
     * Set ip
     *
     * @param string $ip
     * @return BusinessView
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }
    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}