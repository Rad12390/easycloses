<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\LinkRepository")
 */
class Link
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\LinkGroup", inversedBy="links", cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", fetch="EAGER")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business")
     */
    private $business;



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
     * Set source
     *
     * @param string $source
     * @return Link
     */
    public function setSource($source)
    {
        if(strpos($source, 'http://') === false && strpos($source, 'https://') === false) {
            $this->source = 'http://' . $source;
        } else {
            $this->source = $source;
        }

        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Link
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * Set group
     *
     * @param \LocalsBest\UserBundle\Entity\LinkGroup $group
     * @return Link
     */
    public function setGroup(\LocalsBest\UserBundle\Entity\LinkGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \LocalsBest\UserBundle\Entity\LinkGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return Link
     */
    public function setCreatedBy(\LocalsBest\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \LocalsBest\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return mixed
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * @param mixed $business
     */
    public function setBusiness($business)
    {
        $this->business = $business;
    }
}
