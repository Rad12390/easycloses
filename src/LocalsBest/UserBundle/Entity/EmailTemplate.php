<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EmailTemplate
 *
 * @ORM\Table(name="email_templates")
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\EmailTemplateRepository")
 * @UniqueEntity(
 *     fields={"category", "template_number", "business"},
 *     errorPath="template_number",
 *     message="This email template already in use for this category."
 * )
 */
class EmailTemplate
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
     * @var \LocalsBest\UserBundle\Entity\Business
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Business")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="category", type="string")
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="template_number", type="integer")
     */
    private $template_number;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="email_title", type="string", nullable=false)
     */
    private $email_title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="email_body", type="text", nullable=false)
     */
    private $email_body;


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
     * Set category
     *
     * @param string $category
     * @return EmailTemplate
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set template_number
     *
     * @param integer $templateNumber
     * @return EmailTemplate
     */
    public function setTemplateNumber($templateNumber)
    {
        $this->template_number = $templateNumber;

        return $this;
    }

    /**
     * Get template_number
     *
     * @return integer
     */
    public function getTemplateNumber()
    {
        return $this->template_number;
    }

    /**
     * Set email_title
     *
     * @param string $emailTitle
     * @return EmailTemplate
     */
    public function setEmailTitle($emailTitle)
    {
        $this->email_title = $emailTitle;

        return $this;
    }

    /**
     * Get email_title
     *
     * @return string
     */
    public function getEmailTitle()
    {
        return $this->email_title;
    }

    /**
     * Set email_body
     *
     * @param string $emailBody
     * @return EmailTemplate
     */
    public function setEmailBody($emailBody)
    {
        $this->email_body = $emailBody;

        return $this;
    }

    /**
     * Get email_body
     *
     * @return string
     */
    public function getEmailBody()
    {
        return $this->email_body;
    }

    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return EmailTemplate
     */
    public function setBusiness(Business $business = null)
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
