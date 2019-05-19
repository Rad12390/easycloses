<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SplitPaymentHystory
 *
 * @ORM\Table(name="shop_vendor_choices")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\VendorChoiceRepository")
 */
class VendorChoice
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
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="LocalsBest\ShopBundle\Entity\Package", inversedBy="vendorchoice")
     * @ORM\JoinColumn(name="packageId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $packageId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="question_id", type="integer")
     */
    private $question_id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="ask", type="integer")
     */
    private $ask = 0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="require_status", type="integer")
     */
    private $require_status = 0;
    
    
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
     * @param integer $packageId
     */
    public function setpackageId($packageId)
    {
        $this->packageId = $packageId;

        return $this;
    }

    /**
     * Get packageId
     *
     * @return integer 
     */
    public function getpackageId()
    {
        return $this->setpackageId;
    }
    
    /**
     * Set packageId
     *
     * @param integer $packageId
     */
    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;

        return $this;
    }

    /**
     * Get charityPercent
     *
     * @return integer 
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }
    
    
    /**
     * Set $require_status
     *
     * @param integer $require_status
     */
    public function setRequire($require_status)
    {
        $this->require_status = $require_status;

        return $this;
    }

    /**
     * Get $require_status
     *
     * @return integer 
     */
    public function getRequire()
    {
        return $this->require_status;
    }
    
    /**
     * Set ask
     *
     * @param integer $ask
     */
    public function setAsk($ask)
    {
        $this->ask = $ask;

        return $this;
    }

    /**
     * Get Ask
     *
     * @return integer 
     */
    public function getAsk()
    {
        return $this->ask;
    }
}
