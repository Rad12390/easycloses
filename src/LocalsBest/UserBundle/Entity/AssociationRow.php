<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssociationRow
 *
 * @ORM\Table(name="associations_rows")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\AssociationRowRepository")
 */
class AssociationRow
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
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Association")
     * @ORM\JoinColumn(name="association_id", referencedColumnName="id")
     */
    protected $association;

    /**
     * @var string
     * @ORM\Column(name="association_mls_id", type="string")
     */
    protected $associationMlsId;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business", inversedBy="associationRows")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id", nullable=true)
     */
    protected $business;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="associations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

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
     * @return string
     */
    public function getAssociationMlsId()
    {
        return $this->associationMlsId;
    }

    /**
     * @param string $associationMlsId
     */
    public function setAssociationMlsId($associationMlsId)
    {
        $this->associationMlsId = $associationMlsId;
    }

    /**
     * @return Association
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param mixed $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
