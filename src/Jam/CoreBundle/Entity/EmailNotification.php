<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Search
 *
 * @ORM\Table(name="email_notification")
 * @ORM\Entity
 */
class EmailNotification
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $reciever;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="from_id", referencedColumnName="id", nullable=true)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type = '';

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    protected $sentAt;


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
     * Set type
     *
     * @param string $type
     *
     * @return EmailNotification
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
     * Set sentAt
     *
     * @param \DateTime $sentAt
     *
     * @return EmailNotification
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set reciever
     *
     * @param \Jam\UserBundle\Entity\User $reciever
     *
     * @return EmailNotification
     */
    public function setReciever(\Jam\UserBundle\Entity\User $reciever = null)
    {
        $this->reciever = $reciever;

        return $this;
    }

    /**
     * Get reciever
     *
     * @return \Jam\UserBundle\Entity\User
     */
    public function getReciever()
    {
        return $this->reciever;
    }

    /**
     * Set sender
     *
     * @param \Jam\UserBundle\Entity\User $sender
     *
     * @return EmailNotification
     */
    public function setSender(\Jam\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Jam\UserBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }
}
