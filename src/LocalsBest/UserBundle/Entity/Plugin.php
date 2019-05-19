<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Plugin
 *
 * @ORM\Table(name="plugins")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\PluginRepository")
 */
class Plugin
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Item", mappedBy="plugin")
     */
    private $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\UserPlugin", mappedBy="plugin")
     */
    private $userPlugins;


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }


    public function __toString()
    {
        return $this->getName();
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
     * @return Plugin
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
     * Set slug
     *
     * @param string $slug
     * @return Plugin
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Plugin
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
     * @return Plugin
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
     * Add items
     *
     * @param \LocalsBest\ShopBundle\Entity\Item $items
     * @return Plugin
     */
    public function addItem(\LocalsBest\ShopBundle\Entity\Item $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \LocalsBest\ShopBundle\Entity\Item $items
     */
    public function removeItem(\LocalsBest\ShopBundle\Entity\Item $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add userPlugins
     *
     * @param \LocalsBest\ShopBundle\Entity\UserPlugin $userPlugins
     * @return Plugin
     */
    public function addUserPlugin(\LocalsBest\ShopBundle\Entity\UserPlugin $userPlugins)
    {
        $this->userPlugins[] = $userPlugins;

        return $this;
    }

    /**
     * Remove userPlugins
     *
     * @param \LocalsBest\ShopBundle\Entity\UserPlugin $userPlugins
     */
    public function removeUserPlugin(\LocalsBest\ShopBundle\Entity\UserPlugin $userPlugins)
    {
        $this->userPlugins->removeElement($userPlugins);
    }

    /**
     * Get userPlugins
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserPlugins()
    {
        return $this->userPlugins;
    }
}
