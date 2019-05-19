<?php

namespace LocalsBest\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\NotificationBundle\Dbal\Types\NotifiationStatusType;


/**
 * The Notification entity
 *
 * @ORM\Table(name="notifications")
 * @ORM\Entity(repositoryClass="LocalsBest\NotificationBundle\Entity\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="\LocalsBest\NotificationBundle\Entity\UserNotification", mappedBy="notification", cascade={"persist", "remove"})
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $forUsers;
    
    /**
     * @ORM\ManyToMany(targetEntity="\LocalsBest\UserBundle\Entity\User")
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $persons;
    
    /**
     * @ORM\Column
     * @Assert\NotBlank()
     * @var string
     */
    protected $message;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @var int
     */
    protected $created;
    
    /**
     * Target path
     * @var string
     * @ORM\Column(name="target_path")
     */
    protected $targetPath;
    
    /**
     * Target parameters
     * @var string
     * @ORM\Column(type="json_array")
     */
    protected $targetParams;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set message
     *
     * @param string $message
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return Notification
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add forUsers
     *
     * @param \LocalsBest\NotificationBundle\Entity\UserNotification $forUsers
     * @return Notification
     */
    public function addForUser(\LocalsBest\NotificationBundle\Entity\UserNotification $forUsers)
    {
        $this->forUsers[] = $forUsers;

        return $this;
    }

    /**
     * Remove forUsers
     *
     * @param \LocalsBest\NotificationBundle\Entity\UserNotification $forUsers
     */
    public function removeForUser(\LocalsBest\NotificationBundle\Entity\UserNotification $forUsers)
    {
        $this->forUsers->removeElement($forUsers);
    }

    /**
     * Get forUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getForUsers()
    {
        return $this->forUsers;
    }

    /**
     * Add persons
     *
     * @param \LocalsBest\UserBundle\Entity\User $persons
     * @return Notification
     */
    public function addPerson(\LocalsBest\UserBundle\Entity\User $persons)
    {
        $this->persons[] = $persons;

        return $this;
    }

    /**
     * Remove persons
     *
     * @param \LocalsBest\UserBundle\Entity\User $persons
     */
    public function removePerson(\LocalsBest\UserBundle\Entity\User $persons)
    {
        $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersons()
    {
        return $this->persons;
    }
    
    /**
     * Set Persons
     *
     * @return $this
     */
    public function setPersons(\Doctrine\Common\Collections\ArrayCollection $persons)
    {
        $this->persons = $persons;
        
        return $this;
    }
    
    public function setTargetPath($targetPath)
    {
        $this->targetPath = $targetPath;
        
        return $this;
    }
    
    public function getTargetPath()
    {
        return $this->targetPath;
    }
    
    public function setTargetParams($targetParams = array())
    {
        $this->targetParams = json_encode($targetParams);
        
        return $this;
    }
    
    public function getTargetParams()
    {
        return json_decode($this->targetParams, true);
    }
}
