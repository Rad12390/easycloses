<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use LocalsBest\UserBundle\Entity\Plugin;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Item
 *
 * @ORM\Table(name="shop_items")
 * @ORM\Entity(repositoryClass="LocalsBest\ShopBundle\Entity\ItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Item
{
    const REGULAR = 1;
    const PLUGIN = 2;
    const EXTERNAL_LINK = 3;
    const BUCKET = 4;
    const INFO = 4;

    const STATUS_DRAFT = 1;
    const STATUS_APPROVED = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_PUBLISHED = 4;

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
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\Range(min="1")
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="markup", type="integer")
     */
    private $markup = 50;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = self::STATUS_DRAFT;

    /**
     * @var string
     *
     * @ORM\Column(name="external_link", type="text", nullable=true)
     */
    private $externalLink;

    /**
     * @var Plugin
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\Plugin", inversedBy="items")
     * @ORM\JoinColumn(name="plugin_id", referencedColumnName="id")
     */
    private $plugin;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * Many Items have One User.
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="LocalsBest\UserBundle\Entity\User", inversedBy="shopItems")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * One Item have Many Restrictions.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Restriction", mappedBy="item", cascade={"persist", "remove"})
     */
    private $restrictions;

    /**
     * One Item have Many Disposition.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Disposition", mappedBy="item", cascade={"persist", "remove"})
     */
    private $dispositions;

    /**
     * One Item have Many Comments.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Comment", mappedBy="item")
     */
    private $comments;

    /**
     * One Item have Many Images.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\Image", mappedBy="item", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $images;

    /**
     * One Item have Many Sets.
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LocalsBest\ShopBundle\Entity\ItemSet", mappedBy="item")
     */
    private $sets;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_to_vendor", type="boolean")
     */
    private $emailToVendor = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_to_client", type="boolean")
     */
    private $emailToClient = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="text_to_vendor", type="boolean")
     */
    private $textToVendor = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="text_to_client", type="boolean")
     */
    private $textToClient = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notification_to_vendor", type="boolean")
     */
    private $notificationToVendor = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notification_to_client", type="boolean")
     */
    private $notificationToClient = false;


    public function __construct()
    {
        $this->dispositions = new ArrayCollection();
        $this->restrictions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->sets = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->title = '';
        $this->quantity = 0;
    }

    public function __toString()
    {
        return $this->title;
    }


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
     * @return Item
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
     * @param integer $type
     * @return Item
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get text of type
     *
     * @return string
     */
    public function getTypeText()
    {
        $types = [
            1 => 'Regular',
            2 => 'Plugin',
            3 => 'External Link',
            4 => 'Bucket',
            5 => 'Info',
        ];

        return $types[$this->type];
    }

    /**
     * Set externalLink
     *
     * @param string $externalLink
     * @return Item
     */
    public function setExternalLink($externalLink)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $externalLink)) {
            $externalLink = "http://" . $externalLink;
        }
        $this->externalLink = $externalLink;

        return $this;
    }

    /**
     * Get externalLink
     *
     * @return string
     */
    public function getExternalLink()
    {
        return $this->externalLink;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Item
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Item
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param \LocalsBest\UserBundle\Entity\User $createdBy
     * @return Item
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Item
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Get createdBy
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Item
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get text of status
     *
     * @return string
     */
    public function getStatusText()
    {
        $statuses = [
            1 => 'Draft',
            2 => 'Approved',
            3 => 'Archived',
            4 => 'Published',
        ];

        return $statuses[$this->status];
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Add comments
     *
     * @param \LocalsBest\ShopBundle\Entity\Comment $comments
     * @return Item
     */
    public function addComment(Comment $comments)
    {
        $this->comments[] = $comments;

        $comments->setItem($this);

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \LocalsBest\ShopBundle\Entity\Comment $comments
     */
    public function removeComment(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add restrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $restrictions
     * @return Item
     */
    public function addRestriction(Restriction $restrictions)
    {
        $this->restrictions[] = $restrictions;

        $restrictions->setItem($this);

        return $this;
    }
    /**
     * Set restrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $restrictions
     * @return Item
     */
    public function setRestriction($restrictions)
    {
        $this->restrictions = $restrictions;

        //$restrictions->setItem($this);

        return $this;
    }

    /**
     * Remove restrictions
     *
     * @param \LocalsBest\ShopBundle\Entity\Restriction $restrictions
     */
    public function removeRestriction(Restriction $restrictions)
    {
        $this->restrictions->removeElement($restrictions);
    }

    /**
     * Get restrictions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Add images
     *
     * @param Image $images
     * @return Item
     */
    public function addImage(Image $images)
    {
        $this->images[] = $images;

        $images->setItem($this);

        return $this;
    }

    /**
     * Remove images
     *
     * @param \LocalsBest\ShopBundle\Entity\Image $images
     */
    public function removeImage(Image $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set markup
     *
     * @param integer $markup
     * @return Item
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * Get markup
     *
     * @return integer 
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * Add dispositions
     *
     * @param \LocalsBest\ShopBundle\Entity\Disposition $dispositions
     * @return Item
     */
    public function addDisposition(Disposition $dispositions)
    {
        $this->dispositions[] = $dispositions;

        $dispositions->setItem($this);

        return $this;
    }

    /**
     * Remove dispositions
     *
     * @param \LocalsBest\ShopBundle\Entity\Disposition $dispositions
     */
    public function removeDisposition(Disposition $dispositions)
    {
        $this->dispositions->removeElement($dispositions);
    }

    /**
     * Get dispositions
     *
     * @return ArrayCollection
     */
    public function getDispositions()
    {
        return $this->dispositions;
    }

    /**
     * @Assert\Callback
     */
    public function dispositionExists(ExecutionContext $context)
    {
        if ($this->getDispositions()->count() == 0 && $this->status == 4) {
            $context->addViolation('You should add Disposition before publish item.', [], null);
        }
    }

    /**
     * @Assert\Callback
     */
    public function imageExists(ExecutionContext $context)
    {
        if ($this->getImages()->count() == 0) {
            $context->addViolation('You should upload at least 1 image', [], null);
        }
    }

    /**
     * @Assert\Callback
     */
    public function typeChecker(ExecutionContext $context)
    {
        if ($this->getType() == self::PLUGIN && $this->getPlugin() === null) {
            $context->addViolation('You should set Plugin.', [], null);
        } elseif (
            $this->getType() == self::EXTERNAL_LINK
            && ($this->getExternalLink() === null || $this->getExternalLink() == '')
        ) {
            $context->addViolation('You should set External Link.', [], null);
        } elseif ($this->getType() == self::BUCKET && $this->getBucket() === null) {
            $context->addViolation('You should set Bucket.', [], null);
        }
    }

    /**
     * Set plugin
     *
     * @param \LocalsBest\UserBundle\Entity\Plugin $plugin
     * @return Item
     */
    public function setPlugin(Plugin $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return \LocalsBest\UserBundle\Entity\Plugin 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @return ArrayCollection
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * @param ArrayCollection $sets
     */
    public function setSets($sets)
    {
        $this->sets = $sets;
    }

    /**
     * Add sets
     *
     * @param \LocalsBest\ShopBundle\Entity\ItemSet $sets
     * @return Item
     */
    public function addSet(\LocalsBest\ShopBundle\Entity\ItemSet $sets)
    {
        $this->sets[] = $sets;

        return $this;
    }

    /**
     * Remove sets
     *
     * @param \LocalsBest\ShopBundle\Entity\ItemSet $sets
     */
    public function removeSet(\LocalsBest\ShopBundle\Entity\ItemSet $sets)
    {
        $this->sets->removeElement($sets);
    }

    /**
     * Set emailToVendor
     *
     * @param boolean $emailToVendor
     * @return Item
     */
    public function setEmailToVendor($emailToVendor)
    {
        $this->emailToVendor = $emailToVendor;

        return $this;
    }

    /**
     * Get emailToVendor
     *
     * @return boolean 
     */
    public function getEmailToVendor()
    {
        return $this->emailToVendor;
    }

    /**
     * Set emailToClient
     *
     * @param boolean $emailToClient
     * @return Item
     */
    public function setEmailToClient($emailToClient)
    {
        $this->emailToClient = $emailToClient;

        return $this;
    }

    /**
     * Get emailToClient
     *
     * @return boolean 
     */
    public function getEmailToClient()
    {
        return $this->emailToClient;
    }

    /**
     * Set textToVendor
     *
     * @param boolean $textToVendor
     * @return Item
     */
    public function setTextToVendor($textToVendor)
    {
        $this->textToVendor = $textToVendor;

        return $this;
    }

    /**
     * Get textToVendor
     *
     * @return boolean 
     */
    public function getTextToVendor()
    {
        return $this->textToVendor;
    }

    /**
     * Set textToClient
     *
     * @param boolean $textToClient
     * @return Item
     */
    public function setTextToClient($textToClient)
    {
        $this->textToClient = $textToClient;

        return $this;
    }

    /**
     * Get textToClient
     *
     * @return boolean 
     */
    public function getTextToClient()
    {
        return $this->textToClient;
    }

    /**
     * Set notificationToVendor
     *
     * @param boolean $notificationToVendor
     * @return Item
     */
    public function setNotificationToVendor($notificationToVendor)
    {
        $this->notificationToVendor = $notificationToVendor;

        return $this;
    }

    /**
     * Get notificationToVendor
     *
     * @return boolean 
     */
    public function getNotificationToVendor()
    {
        return $this->notificationToVendor;
    }

    /**
     * Set notificationToClient
     *
     * @param boolean $notificationToClient
     * @return Item
     */
    public function setNotificationToClient($notificationToClient)
    {
        $this->notificationToClient = $notificationToClient;

        return $this;
    }

    /**
     * Get notificationToClient
     *
     * @return boolean 
     */
    public function getNotificationToClient()
    {
        return $this->notificationToClient;
    }
}
