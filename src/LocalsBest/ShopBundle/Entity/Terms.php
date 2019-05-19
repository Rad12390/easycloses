<?php
namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Quotes
 *
 * @ORM\Table(name="shop_terms_and_conditions")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\TermsRepository")
 */
class Terms
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="terms")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $userid;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status= false;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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
     * Set userid
     *
     * @param \LocalsBest\UserBundle\Entity\User $userid
     */
    public function setUserId($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userid;
    }
    
    /**
     * Set $status
     *
     * @param integer $status
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get $status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
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
