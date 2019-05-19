<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocalsBest\UserBundle\Entity\SurveyQuestion
 *
 * @ORM\Table(name="surveys_results" )
 * @ORM\Entity()
 *
 */
class SurveyResult
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="results", cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $users;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Survey", inversedBy="results", cascade={"all"})
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    protected $survey;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\SurveyAnswer", inversedBy="results", cascade={"all"})
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     */
    protected $answers;

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
    public function getUser()
    {
        return $this->users;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->users = $user;
    }

    /**
     * @return mixed
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param mixed $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answers;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answers = $answer;
    }

}