<?php
namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Quotes
 *
 * @ORM\Table(name="shop_custom_quotes")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\QuotesRepository")
 */
class Quotes
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Package", inversedBy="quotedPackage")
     * @ORM\JoinColumn(name="packageId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $packageId;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="quotedUser")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $userid;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="vendor")
     * @ORM\JoinColumn(name="vendorId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $vendorid;
    
    /**
     * @var string
     *
     * @ORM\Column(name="quote", type="text")
     */
    private $quote;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

   
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
     * Set packageId
     *
     * @param \LocalsBest\ShopBundle\Entity\Package $owner
     * @return Business
     */
    public function setPackageId($packageId)
    {
        $this->packageId = $packageId;

        return $this;
    }

    /**
     * Get packageId
     *
     * @return \LocalsBest\ShopBundle\Entity\Package
     */
    public function getPackageId()
    {
        return $this->packageId;
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
     * Set vendorid
     *
     * @param \LocalsBest\UserBundle\Entity\User $vendorid
     * @return Business
     */
    public function setVendorId($vendorid)
    {
        $this->vendorid = $vendorid;

        return $this;
    }

    /**
     * Get vendorid
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getVendorId()
    {
        return $this->vendorid;
    }
    
    /**
     * Set $quote
     *
     * @param string $quote
     */
    public function setQuote($quote) {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getQuote() {
        return $this->quote;
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

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
}
