<?php

namespace LocalsBest\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role_old")
 * @ORM\Entity()
 */
class Role implements RoleInterface
{
    const ROLE_ADMIN                = 1;
    const ROLE_CUSTOMER_SERVIC      = 2;
    const ROLE_NETWORK_MANAGER      = 3;
    const ROLE_MANAGER              = 4;
    const ROLE_ASSIST_MANAGER       = 5;
    const ROLE_TEAM_LEADER          = 6;
    const ROLE_AGENT                = 7;
    const ROLE_VENDOR               = 7;
    const ROLE_CLIENT               = 8;
//    const ROLE_PDF_CREATOR              = 11;
//    const ROLE_PDF_SENDER               = 12;
//    const ROLE_PDF_SIGNEE               = 13;


    const NAME_ADMIN                = 'Admin';
    const NAME_CUSTOMER_SERVIC      = 'Customer Service(CSR)';
    const NAME_NETWORK_MANAGER      = 'Network Manager';
    const NAME_MANAGER              = 'Manager';
    const NAME_ASSIST_MANAGER       = 'Assistant Manager';
    const NAME_TEAM_LEADER          = 'Team Leader';
    const NAME_AGENT                = 'Agent';
    const NAME_VENDOR               = 'Vendor';
    const NAME_CLIENT               = 'Client';
//    const NAME_CREATOR              = 'Pdf_creator';
//    const NAME_SENDER               = 'Pdf_sender';
//    const NAME_SIGNEE               = 'Pdf_signee';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=64)
     */
    private $role;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $level;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="role")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }

    // ... getters and setters for each property

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
     * @return Role
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
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Add users
     *
     * @param \LocalsBest\UserBundle\Entity\User $users
     * @return Role
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \LocalsBest\UserBundle\Entity\User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }



    /**
     * Set level
     *
     * @param int $level
     * @return Role
     */
    public function setLevel($level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \LocalsBest\UserBundle\Entity\Role
     */
    public function getLevel()
    {
        return $this->level;
    }
}
