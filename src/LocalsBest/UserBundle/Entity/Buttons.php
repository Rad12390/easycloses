<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tooltip
 *
 * @ORM\Table(name="buttons")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\ButtonsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Buttons
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
     * @ORM\Column(name="location", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="text")
     */
    private $link;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="clicked_times", type="integer")
     */
    private $clicked_time=0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
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
     * Set name
     *
     * @param string $name
     * @return Tooltip
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Tooltip
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
     * Set name
     *
     * @param string $name
     * @return Tooltip
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }
    

    /**
     * Set body
     *
     * @param string $body
     * @return Tooltip
     */
    public function setClicked($clicked_time)
    {
        $this->clicked_time = $clicked_time;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getClicked()
    {
        return $this->clicked_time;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Tooltip
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
     * @return Tooltip
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

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}
