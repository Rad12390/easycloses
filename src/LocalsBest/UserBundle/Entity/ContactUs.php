<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ContactUs
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ContactUsRepository")
 */
class ContactUs
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
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

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
     * Set userName
     *
     * @param string $userName
     * @return ContactUs
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
     * Set email
     *
     * @param string $email
     * @return ContactUs
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
     * @return ContactUs
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
     * @return ContactUs
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
     * @return ContactUs
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
     * @param \LocalsBest\UserBundle\Entity\Product $product
     * @return ContactUs
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \LocalsBest\UserBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set industryType
     *
     * @param string $industryType
     * @return ContactUs
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
     * @return ContactUs
     */
    public function setMlsBoard($mlsBoard)
    {
        $this->mlsBoard = $mlsBoard;

        return $this;
    }

    /**
     * Get mlsBoard
     *
     * @return string 
     */
    public function getMlsBoard()
    {
        return $this->mlsBoard;
    }
}
