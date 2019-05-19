<?php

namespace LocalsBest\WordPressApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LocalsBest\UserBundle\Entity\AllContact;

/**
 * Content
 *
 * @ORM\Table(name="wp_content")
 * @ORM\Entity(repositoryClass="LocalsBest\WordPressApiBundle\Entity\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Content
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
     * @ORM\Column(name="type", type="string")
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $created_at;


    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
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
     * Set type
     *
     * @param string $type
     * @return Content
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Content
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set contact
     *
     * @param \LocalsBest\UserBundle\Entity\AllContact $contact
     * @return Content
     */
    public function setContact(AllContact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \LocalsBest\UserBundle\Entity\AllContact 
     */
    public function getContact()
    {
        return $this->contact;
    }
}
