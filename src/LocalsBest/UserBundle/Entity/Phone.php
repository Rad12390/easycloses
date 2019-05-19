<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\UserBundle\Dbal\Types\PhoneTypeType;


/**
 * LocalsBest\UserBundle\Entity\Phone
 *
 * @ORM\Table(name="phones")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PhoneRepository")
 */
class Phone
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "7",
     *      max = "20",
     *      minMessage = "Your telephone must be at least {{ limit }} characters length",
     *      maxMessage = "Your telephone cannot be longer than {{ limit }} characters length"
     * )
     *  @Assert\Regex(
     *     pattern="/\d/",
     *     match=true,
     *     message="Your phone number should contain numbers only"
     * )
     * @var Integer 
     */
    protected $number;
    
    /**
     * @ORM\Column(type="PhoneTypeType", nullable=false)
     * @Assert\NotBlank()
     * @DoctrineAssert\Enum(entity="LocalsBest\UserBundle\Dbal\Types\PhoneTypeType")
     */
    protected $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Contact", inversedBy="phones")
     * @var integer
     */
    protected $contact;
    
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
     * Set number
     *
     * @param integer $number
     * @return Phone
     */
    public function setNumber($number)
    {
        $this->number = $this->clearPhoneNumber($number);
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        $number = $this->clearPhoneNumber($this->number);
        if ( !is_null($this->getContact()) && ((!is_null($this->getContact()->getUser()) && count($this->getContact()->getUser()->getBusinesses()) > 0 && $this->getContact()->getUser()->getBusinesses()->first()->getId() == 15) || $this->getContact()->getId() == 38)) {
            return substr($number, 0, 3) . '.' . substr($number, 3, 3) . '.' . substr($number, 6, 4);
        } else {
            $result = strlen($number) > 0 ? ('(' . substr($number, 0, 3) . ') ' . substr($number, 3, 3) . '-' . substr($number, 6, 4)) : '';
            return $result;
        }
    }

    public function getClearNumber()
    {
        $number = $this->clearPhoneNumber($this->number);

        return $number;
    }
    
    public function getExtendedNumber()
    {
        $choices = array_flip(PhoneTypeType::getChoices());

        return $this->getNumber() . ' (' . $choices[$this->type] . ')';
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Phone
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set contact
     *
     * @param \LocalsBest\UserBundle\Entity\Contact $contact
     * @return Phone
     */
    public function setContact(\LocalsBest\UserBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return integer
     */
    public function getContact()
    {
        return $this->contact;
    }

    protected function clearPhoneNumber($number)
    {
        return str_replace(['(', ')', '-', ' ', '.'], '', $number);
    }
}
