<?php

namespace Jam\MessageBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;

/**
 * @MongoDB\Document
 */
class Inbox
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Int
     * @var \Jam\UserBundle\Entity\User
     */
    protected $user;

    /** @EmbedMany(targetDocument="Message") */
    private $messages = array();

    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param int $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return int $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add message
     *
     * @param Jam\MessageBundle\Document\Message $message
     */
    public function addMessage(\Jam\MessageBundle\Document\Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * Remove message
     *
     * @param Jam\MessageBundle\Document\Message $message
     */
    public function removeMessage(\Jam\MessageBundle\Document\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return Doctrine\Common\Collections\Collection $messages
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
