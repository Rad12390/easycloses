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
 * @ORM\Table(name="surveys_answers" )
 * @ORM\Entity()
 *
 */
class SurveyAnswer
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Survey", inversedBy="answers", cascade={"all"})
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    protected $survey;

    /**
     * @ORM\OneToMany(targetEntity="LocalsBest\UserBundle\Entity\SurveyResult", mappedBy="answers", cascade={"all"})
     */
    protected $results;

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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
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
    public function setSurvey(\LocalsBest\UserBundle\Entity\Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }
}