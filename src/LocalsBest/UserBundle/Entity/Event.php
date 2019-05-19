<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use LocalsBest\CommonBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * LocalsBest\UserBundle\Entity\Event
 *
 * @ORM\Table(name="events" )
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\EventRepository")
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="shares",
 *          joinTable=@ORM\JoinTable(
 *              name="event_shares",
 *              joinColumns=@ORM\JoinColumn(name="event_id"),
 *              inverseJoinColumns=@ORM\JoinColumn(name="share_id")
 *          )
 *      )
 * }),
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="notes",
 *          joinTable=@ORM\JoinTable(
 *              name="event_notes",
 *              joinColumns=@ORM\JoinColumn(name="event_id"),
 *              inverseJoinColumns=@ORM\JoinColumn(name="note_id")
 *          )
 *      )
 * })
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false)
 * @ORM\HasLifecycleCallbacks()
 */
class Event extends BaseEntity
{   
    /**
     * @ORM\Column(type="string", length=100, nullable= true)
     */
    protected $title;
    
    /**
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;
    
    /**
     * @ORM\Column(type="boolean", nullable= true)
     * 
     */
    protected $isRequired;
    
    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy HH:mm a"}, nullable = true)
     */
    protected $time;

    /**
     * @ORM\Column(type="datetime", options={"format"="MM/dd/yyyy HH:mm a"}, nullable = true)
     */
    protected $end_time;
    
    /**
     * @ORM\Column(type="string", length=100, nullable= true)
     */
    protected $type;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="assignedTo", cascade={"all"})
     * @var \LocalsBest\UserBundle\Entity\User 
     */
    protected $assignedTo;
    
    /**
     * @ORM\Column(type="string", length=100, nullable= true)
     */
    protected $custom;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $completed;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $alert;
    
    
    
    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\EventAlert", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     * @var ArrayCollection
     */
    protected $alerts;
    
    
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="events", cascade="persist")
     * @var type 
     */
    protected $user;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $eventStatus;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->slug = 'event-' . rand(1, 99999) . rand(1, 99999);
        $this->alert = false;
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->assignedTo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isRequired = FALSE;
        $this->eventStatus = FALSE;
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * Add alerts
     *
     * @param \LocalsBest\UserBundle\Entity\EventAlert $alerts
     * @return Event
     */
    public function addAlert(\LocalsBest\UserBundle\Entity\EventAlert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \LocalsBest\UserBundle\Entity\EventAlert $alerts
     */
    public function removeAlert(\LocalsBest\UserBundle\Entity\EventAlert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        // replace non letter or digits by -
        $title = preg_replace('~[^\\pL\d]+~u', '-', $title);

        // trim
        $title = trim($title, '-');

        // transliterate
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);

        // lowercase
        $title = strtolower($title);

        // remove unwanted characters
        $title = preg_replace('~[^-\w]+~', '', $title);

        $slug = $title ."-". time() . rand() . rand();
        
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Event
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Event
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set custom
     *
     * @param string $custom
     * @return Event
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Get custom
     *
     * @return string 
     */
    public function getCustom()
    {
        return $this->custom;
    }


    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    

    /**
     * Add assignedTo
     *
     * @param \LocalsBest\UserBundle\Entity\User $assignedTo
     * @return Event
     */
    public function addAssignedTo(\LocalsBest\UserBundle\Entity\User $assignedTo)
    {
        $this->assignedTo[] = $assignedTo;

        return $this;
    }

    /**
     * Remove assignedTo
     *
     * @param \LocalsBest\UserBundle\Entity\User $assignedTo
     */
    public function removeAssignedTo(\LocalsBest\UserBundle\Entity\User $assignedTo)
    {
        $this->assignedTo->removeElement($assignedTo);
    }
    
    public function setCompleted($completed)
    {
        $this->completed = $completed;
        
        return $this;
    }
    
    public function getCompleted()
    {
        return $this->completed;
    }
    
    /**
     * Set alert
     *
     * @param boolean $alert
     * @return Event
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * Get alert
     *
     * @return boolean 
     */
    public function getAlert()
    {
        return $this->alert;
    }
    
    /**
     * Set isRequired
     *
     * @param boolean $isRequired
     * @return Event
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return boolean 
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }
    
    
    /**
     * Set user
     *
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return Event
     */
    public function setUser(\LocalsBest\UserBundle\Entity\User $user = null)
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
     * Set eventStatus
     *
     * @param string $eventStatus 
    * @return Event
     */
    public function setEventStatus($eventStatus)
    {
        $this->eventStatus = $eventStatus;

        return $this;
    }

    /**
     * Get eventStatus
     *
     * @return string 
     */
    public function getEventStatus()
    {
        return $this->eventStatus;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @param mixed $end_time
     */
    public function setEndTime($end_time)
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        if ($this->getId() === null) {
            $this->setCreated(time());
        }

        $this->setUpdated(time());
    }

    public function getColorCircle()
    {
        if ($this->getStatus() !== null) {
            if (
                strtolower($this->getStatus()->getStatus() == 'open')
                && $this->getEndTime() < new \DateTime()
            ) {
                $color = "#F1C40F";
            } elseif (
                strtolower($this->getStatus()->getStatus() == 'open')
                && $this->getEndTime() > new \DateTime()
            ) {
                $color = "#32c5d2";
            } else {
                $color = "#3598dc";
            }
        } else {
            $color = '#000';
        }

        return '<span class="badge" style="color: ' . $color . '; background-color: '. $color . '"> 3 </span>';
    }
}
