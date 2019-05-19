<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LocalsBest\CommonBundle\Entity\BaseEntity;

/**
 * LocalsBest\UserBundle\Entity\Team
 *
 * @ORM\Table(name="teams" )
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="LocalsBest\UserBundle\Entity\User", cascade={"all"})
     */
    protected $leader;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\User", mappedBy="team")
     */
    protected $agents;

    public function __construct()
    {
        $this->agents  = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * @param mixed $leader
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;
    }

    /**
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @return Team
     */
    public function addAgent(\LocalsBest\UserBundle\Entity\User $user)
    {
        $this->agents[] = $user;
        return $this;
    }

    /**
     * Remove agent
     * @param \LocalsBest\UserBundle\Entity\User $agent
     */
    public function removeAgent(\LocalsBest\UserBundle\Entity\User $agent)
    {
        $this->agents->removeElement($agent);
    }

    /**
     * Get agents
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgents()
    {
        return $this->agents;
    }

}