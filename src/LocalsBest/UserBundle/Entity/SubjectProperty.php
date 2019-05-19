<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubjectProperty
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\SubjectPropertyRepository")
 */
class SubjectProperty
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
     * @ORM\Column(type="string", length=16, nullable= true)
     */
    private $mls;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zip;

    /**
     * @ORM\Column(type="string", length=2, nullable= true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=64, nullable= true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10, nullable= true)
     */
    private $unit;

    /**
     * @ORM\Column(type="string", length=127, nullable= true)
     */
    private $street;


    public function __construct($data = null)
    {
        if ($data !== null && is_array($data)) {
            $this->mls = isset($data['mls']) ? $data['mls'] : null;
            $this->street = isset($data['street']) ? $data['street'] : null;
            $this->unit = isset($data['unit']) ? $data['unit'] : null;
            $this->city = isset($data['city']) ? $data['city'] : null;
            $this->state = isset($data['state']) ? $data['state'] : null;
            $this->zip = isset($data['zip']) ? $data['zip'] : null;
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
     * Set mls
     *
     * @param string $mls
     * @return SubjectProperty
     */
    public function setMls($mls)
    {
        $this->mls = $mls;

        return $this;
    }

    /**
     * Get mls
     *
     * @return string 
     */
    public function getMls()
    {
        return $this->mls;
    }

    /**
     * Set zip
     *
     * @param integer $zip
     * @return SubjectProperty
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return integer 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return SubjectProperty
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return SubjectProperty
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return SubjectProperty
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return SubjectProperty
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    public function getFullAddress()
    {
        return $this->street . ', ' . $this->unit . ', ' . $this->city . ', ' . $this->state . ', ' .$this->zip;
    }
}
