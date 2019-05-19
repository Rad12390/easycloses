<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ContactUs
 *
 * @ORM\Table(name="shop_user_requests")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\SkuContactUsRepository")
 */
class SkuContactUs
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
     * @ORM\Column(name="user_name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="128", min="4")
     */
    private $userName;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="service_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $serviceName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max="64", min="6")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=16)
     * @Assert\NotBlank()
     * @Assert\Length(max="14", min="10")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_type", type="string", length=128, nullable=true)
     * @Assert\Length(max="128", min="4")
     */
    private $industryType;

    /**
     * @var string
     *
     * @ORM\Column(name="mls_board", type="string", length=128, nullable=true)
     * @Assert\Length(max="128", min="4")
     */
    private $mlsBoard;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text")
     * @Assert\NotBlank()
     */
    private $note;

    /**
     * @var Sku
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Sku")
     * @ORM\JoinColumn(name="sku_id", referencedColumnName="id")
     */
    private $sku;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(name="isread", type="boolean")
     */
    private $isread;


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
     * Set userName
     *
     * @param string $userName
     * @return SkuContactUs
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->userName;
    }
    
    
    /**
     * Set serviceName
     *
     * @param string $serviceName
     * @return SkuContactUs
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string 
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SkuContactUs
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return SkuContactUs
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return SkuContactUs
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SkuContactUs
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
     * Set product
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $sku
     * @return SkuContactUs
     */
    public function setSku(Sku $sku = null)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return \LocalsBest\ShopBundle\Entity\Sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set industryType
     *
     * @param string $industryType
     * @return SkuContactUs
     */
    public function setIndustryType($industryType)
    {
        $this->industryType = $industryType;

        return $this;
    }

    /**
     * Get industryType
     *
     * @return string 
     */
    public function getIndustryType()
    {
        return $this->industryType;
    }

    /**
     * Set mlsBoard
     *
     * @param string $mlsBoard
     * @return SkuContactUs
     */
    public function setMlsBoard($mlsBoard)
    {
        $this->mlsBoard = $mlsBoard;

        return $this;
    }

    /**
     * Get mlsBoard
     *
     * @return boolean 
     */
    public function getMlsBoard()
    {
        return $this->mlsBoard;
    }
            
    public function setIsRead($isread)
    {
        $this->isread = $isread;

        return $this;
    }

    /**
     * Get isread
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->isread;
    }
}
