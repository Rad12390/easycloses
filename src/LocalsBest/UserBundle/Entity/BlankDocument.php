<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BlankDocument
 *
 * @ORM\Table(name="blank_documents", uniqueConstraints={@UniqueConstraint(name="title_type_unique", columns={"title", "type"})})
 * @ORM\Entity(repositoryClass="LocalsBest\UserBundle\Entity\BlankDocumentRepository")
 * @UniqueEntity(
 *     fields={"title", "type"},
 *     errorPath="title",
 *     message="You already create this pair of Title-Type."
 * )
 * @Vich\Uploadable
 */
class BlankDocument
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=128)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @Vich\UploadableField(mapping="blank_docs", fileNameProperty="fileName")
     *
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File $file
     * @Assert\File(
     *     maxSize = "204890k",
     *     mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "This program supports PDFS and Images. Please change your file type and try again"
     * )
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;

    /**
     * @var Business
     *
     * @ORM\ManyToOne(targetEntity="Business")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * @var array
     *
     * @ORM\Column(name="able_roles", type="json_array")
     */
    private $ableRoles = ['ROLE_MANAGER', 'ROLE_ASSIST_MANAGER', 'ROLE_TEAM_LEADER', 'ROLE_AGENT'];

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
     * Set title
     *
     * @param string $title
     * @return BlankDocument
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return BlankDocument
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return BlankDocument
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set business
     *
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @return BlankDocument
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile($file = null)
    {
        if (!$file instanceof \Symfony\Component\HttpFoundation\File\File) {
            return $this;
        }

        $this->file = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set ableRoles
     *
     * @param array $ableRoles
     * @return BlankDocument
     */
    public function setAbleRoles($ableRoles)
    {
        $this->ableRoles = $ableRoles;

        return $this;
    }

    /**
     * Get ableRoles
     *
     * @return array 
     */
    public function getAbleRoles()
    {
        return $this->ableRoles;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->file === null) {
            $context
                ->buildViolation('File was not set.')
                ->atPath('title')
                ->addViolation()
            ;
        }
    }
}
