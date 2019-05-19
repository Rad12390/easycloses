<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vendors_customer")
 */
class Vendorcustomer
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $customerid;

    /**
     * @ORM\Column(type="text")
     */
    private $connectcustid;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $connectaccountid;


    /**
     * @ORM\Column(type="text")
     */
    private $accountemail;

    /**
     * @ORM\Column(type="text")
     */
    private $datetime;

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
     * Set customerid
     *
     * @param string $customerid
     * @return Vendorcustomer
     */
    public function setCustomerid($customerid)
    {
        $this->customerid = $customerid;

        return $this;
    }

    /**
     * Get customerid
     *
     * @return string 
     */
    public function getCustomerid()
    {
        return $this->customerid;
    }

    /**
     * Set connectcustid
     *
     * @param string $connectcustid
     * @return Vendorcustomer
     */
    public function setConnectcustid($connectcustid)
    {
        $this->connectcustid = $connectcustid;

        return $this;
    }

    /**
     * Get connectcustid
     *
     * @return string 
     */
    public function getConnectcustid()
    {
        return $this->connectcustid;
    }

    /**
     * Set connectaccountid
     *
     * @param string $connectaccountid
     * @return Vendorcustomer
     */
    public function setConnectaccountid($connectaccountid)
    {
        $this->connectaccountid = $connectaccountid;

        return $this;
    }

    /**
     * Get connectaccountid
     *
     * @return string 
     */
    public function getConnectaccountid()
    {
        return $this->connectaccountid;
    }

    /**
     * Set accountemail
     *
     * @param string $accountemail
     * @return Vendorcustomer
     */
    public function setAccountemail($accountemail)
    {
        $this->accountemail = $accountemail;

        return $this;
    }

    /**
     * Get accountemail
     *
     * @return string 
     */
    public function getAccountemail()
    {
        return $this->accountemail;
    }

    /**
     * Set datetime
     *
     * @param string $datetime
     * @return Vendorcustomer
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return string 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }
}
