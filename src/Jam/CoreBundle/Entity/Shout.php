<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Jam
 *
 * @ORM\Table(name="shouts")
 * @ORM\Entity
 */
class Shout
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
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="shouts")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
     */
    private $creator;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $text;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

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
     * Set creator
     *
     * @param \Jam\UserBundle\Entity\User $creator
     * @return Shout
     */
    public function setCreator(\Jam\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Jam\UserBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Shout
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
     * @return Shout
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
     * Set text
     *
     * @param string $text
     * @return Shout
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    public function getTextFrontend()
    {
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

        if(preg_match($reg_exUrl, $this->text, $url)) {
            return preg_replace($reg_exUrl, "<a href=" .$url[0] ." target='blank'>" .$url[0] ."</a> ", $this->text);
        } else {
            return $this->text;
        }
    }

    public function getCreatedAtAgo()
    {
        $to_time = $this->getCreatedAt()->getTimestamp();
        $from_time = time();

        $minutes = abs(($to_time - $from_time) / 60);

        if ($minutes > 60) {
            //show hours
            $hours = round($minutes / 60);
            //if hours > 24 show date
            if ($hours > 24) {
                return date("Y-m-d H:i", $to_time);
            } else {
                if ($hours == 1) {
                    return $hours . ' hour ago';
                }else {
                    return $hours . ' hours ago';
                }
            }

        } else {
            // show minutes
            if (round($minutes) == 0) {
                return "just now";
            } else {
                return round($minutes) . " min ago";
            }
        }
    }
}
