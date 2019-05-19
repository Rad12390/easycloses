<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table(name="shop_tags")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\TagRepository")
 */
class Tag
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
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="LocalsBest\ShopBundle\Entity\Sku", mappedBy="tags")
     */
    private $skus;


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
     * @return Tag
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
     * Constructor
     */
    public function __construct()
    {
        $this->skus = new ArrayCollection();
    }

    /**
     * Add skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     * @return Tag
     */
    public function addSku(Sku $skus)
    {
        $this->skus[] = $skus;

        return $this;
    }

    /**
     * Remove skus
     *
     * @param Sku $skus
     */
    public function removeSku(Sku $skus)
    {
        $this->skus->removeElement($skus);
    }

    /**
     * Get skus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkus()
    {
        return $this->skus;
    }

    /**
     * Add skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     * @return Tag
     */
    public function addSkus(\LocalsBest\ShopBundle\Entity\Sku $skus)
    {
        $this->skus[] = $skus;

        return $this;
    }

    /**
     * Remove skus
     *
     * @param \LocalsBest\ShopBundle\Entity\Sku $skus
     */
    public function removeSkus(\LocalsBest\ShopBundle\Entity\Sku $skus)
    {
        $this->skus->removeElement($skus);
    }
}
