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

    /** @EmbedMany(targetDocument="Conversation") */
    private $conversations = array();


    public function __construct()
    {
        $this->conversations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add conversation
     *
     * @param Jam\MessageBundle\Document\Conversation $conversation
     */
    public function addConversation(\Jam\MessageBundle\Document\Conversation $conversation)
    {
        $this->conversations[] = $conversation;
    }

    /**
     * Remove conversation
     *
     * @param Jam\MessageBundle\Document\Conversation $conversation
     */
    public function removeConversation(\Jam\MessageBundle\Document\Conversation $conversation)
    {
        $this->conversations->removeElement($conversation);
    }

    /**
     * Get conversations
     *
     * @return Doctrine\Common\Collections\Collection $conversations
     */
    public function getConversations()
    {
        return $this->conversations;
    }
}
