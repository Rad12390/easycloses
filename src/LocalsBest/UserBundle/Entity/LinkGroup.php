<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LinkGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\LinkGroupRepository")
 */
class LinkGroup
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
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\Link", mappedBy="group", cascade={"persist"})
     */
    private $links;

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
     * Constructor
     */
    public function __construct()
    {
        $this->links = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set title
     *
     * @param string $title
     * @return LinkGroup
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
     * Add links
     *
     * @param \LocalsBest\UserBundle\Entity\Link $links
     * @return LinkGroup
     */
    public function addLink(\LocalsBest\UserBundle\Entity\Link $links)
    {
        $this->links[] = $links;

        return $this;
    }

    /**
     * Remove links
     *
     * @param \LocalsBest\UserBundle\Entity\Link $links
     */
    public function removeLink(\LocalsBest\UserBundle\Entity\Link $links)
    {
        $this->links->removeElement($links);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks()
    {
        return $this->links;
    }
}
