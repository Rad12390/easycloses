<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocalsBest\UserBundle\Entity\DocRule
 *
 * @ORM\Table(name="doc_rules" )
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\DocRuleRepository")
 * 
 */
class DocRule 
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $creating;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $represent;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $propertyType;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $status;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $transactionType;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $yearBuiltBefore;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $yearBuiltAfter;
    
    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     * 
     */
    protected $documentName;
    
    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business")
     */
    protected $business;


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
     * Set creating
     *
     * @param string $creating
     * @return DocRule
     */
    public function setCreating($creating)
    {
        $this->creating = $creating;

        return $this;
    }

    /**
     * Get creating
     *
     * @return string 
     */
    public function getCreating()
    {
        return $this->creating;
    }

    /**
     * Set represent
     *
     * @param string $represent
     * @return DocRule
     */
    public function setRepresent($represent)
    {
        $this->represent = $represent;

        return $this;
    }

    /**
     * Get represent
     *
     * @return string 
     */
    public function getRepresent()
    {
        return $this->represent;
    }

    /**
     * Set propertyType
     *
     * @param string $propertyType
     * @return DocRule
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get propertyType
     *
     * @return string 
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return DocRule
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     * @return DocRule
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return string 
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set yearBuiltBefore
     *
     * @param string $yearBuiltBefore
     * @return DocRule
     */
    public function setYearBuiltBefore($yearBuiltBefore)
    {
        if($yearBuiltBefore == '') {
            $yearBuiltBefore = 'Any';
        }

        $this->yearBuiltBefore = $yearBuiltBefore;

        return $this;
    }

    /**
     * Get yearBuiltBefore
     *
     * @return string 
     */
    public function getYearBuiltBefore()
    {
        return $this->yearBuiltBefore;
    }

    /**
     * Set yearBuiltAfter
     *
     * @param string $yearBuiltAfter
     * @return DocRule
     */
    public function setYearBuiltAfter($yearBuiltAfter)
    {
        if($yearBuiltAfter == '') {
            $yearBuiltAfter = 'Any';
        }
        $this->yearBuiltAfter = $yearBuiltAfter;

        return $this;
    }

    /**
     * Get yearBuiltAfter
     *
     * @return string 
     */
    public function getYearBuiltAfter()
    {
        return $this->yearBuiltAfter;
    }

    /**
     * Set documentName
     *
     * @param string $documentName
     * @return DocRule
     */
    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;

        return $this;
    }

    /**
     * Get documentName
     *
     * @return string 
     */
    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return DocRule
     */
    public function setBusiness(\LocalsBest\UserBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \LocalsBest\UserBundle\Entity\Business 
     */
    public function getBusiness()
    {
        return $this->business;
    }
}
