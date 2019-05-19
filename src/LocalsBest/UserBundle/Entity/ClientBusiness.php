<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClientBusiness
 *
 * @ORM\Table(name="client_business")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ClientBusinessRepository")
 */
class ClientBusiness
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
     * @ORM\Column(name="name", type="string", length=164, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_name", type="string", length=255, nullable=true)
     */
    private $managerName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64, nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=16, nullable=true)
     * @Assert\Length(max="10")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=128, nullable=true)
     */
    private $website;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="client_business")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var IndustryType
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\IndustryType", inversedBy="client_businesses")
     * @ORM\JoinColumn(name="industry_type_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $industry_type;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\State", inversedBy="client_businesses")
     * @Assert\Count(min="1")
     */
    private $states;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\City", inversedBy="client_businesses")
     */
    private $cities;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->states = new ArrayCollection();
        $this->cities = new ArrayCollection();
    }


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
     * Set name
     *
     * @param string $name
     * @return ClientBusiness
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set managerName
     *
     * @param string $managerName
     * @return ClientBusiness
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;

        return $this;
    }

    /**
     * Get managerName
     *
     * @return string 
     */
    public function getManagerName()
    {
        return $this->managerName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ClientBusiness
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
     * @return ClientBusiness
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
     * Set website
     *
     * @param string $website
     * @return ClientBusiness
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return ClientBusiness
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
     * Set industry_type
     *
     * @param \LocalsBest\UserBundle\Entity\IndustryType $industryType
     * @return ClientBusiness
     */
    public function setIndustryType(IndustryType $industryType = null)
    {
        $this->industry_type = $industryType;

        return $this;
    }

    /**
     * Get industry_type
     *
     * @return \LocalsBest\UserBundle\Entity\IndustryType 
     */
    public function getIndustryType()
    {
        return $this->industry_type;
    }

    /**
     * Add states
     *
     * @param \LocalsBest\UserBundle\Entity\State $states
     * @return ClientBusiness
     */
    public function addState(State $states)
    {
        $this->states[] = $states;

        return $this;
    }

    /**
     * Remove states
     *
     * @param \LocalsBest\UserBundle\Entity\State $states
     */
    public function removeState(State $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Add cities
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     * @return ClientBusiness
     */
    public function addCity(City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param \LocalsBest\UserBundle\Entity\City $cities
     */
    public function removeCity(City $cities)
    {
        $this->cities->removeElement($cities);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCities()
    {
        return $this->cities;
    }

    public function getData()
    {
        $result = [];

        foreach ($this->getCities() as $city) {
            $result[$city->getId()] = $city->getName();
        }
        return $result;
    }
}
