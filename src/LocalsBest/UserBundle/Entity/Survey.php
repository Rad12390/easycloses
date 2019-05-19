<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LocalsBest\UserBundle\Entity\SurveyQuestion
 *
 * @ORM\Table(name="surveys" )
 * @ORM\Entity()
 *
 */
class Survey
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
    protected $question;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\SurveyAnswer", mappedBy="survey", cascade={"all"})
     */
    protected $answers;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Timestampable(on="create")
     * @var integer
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", cascade="persist")
     * @var \LocalsBest\UserBundle\Entity\User
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="string", length=30)
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\SurveyResult", mappedBy="survey", cascade={"all"})
     */
    protected $results;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Add Answer
     *
     * @param \LocalsBest\UserBundle\Entity\SurveyAnswer $answer
     * @return Survey
     */
    public function addAnswer(SurveyAnswer $answer)
    {
        $this->answers[] = $answer;
        return $this;
    }

    /**
     * Remove answer
     *
     * @param \LocalsBest\UserBundle\Entity\SurveyAnswer $answer
     */
    public function removeAnswer(SurveyAnswer $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }
}