<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Jam
 *
 * @ORM\Table(name="searches")
 * @ORM\Entity
 */
class Search
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
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="jamsCreator")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
     */
    private $creator;

    /**
     * @var string
     *
     * @ORM\Column(name="genres", type="text", length=1000)
     */
    private $genres = '';

    /**
     * @var string
     *
     * @ORM\Column(name="instruments", type="text", length=1000)
     */
    private $instruments = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="is_teacher", type="boolean", length=1)
     */
    private $isTeacher;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_subscribed", type="boolean", length=1)
     */
    private $isSubscribed = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="distance", type="integer")
     */
    private $distance;

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
     * @var array
     *
     * @ORM\Column(name="users", type="array", nullable=true)
     */
    private $users;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = array();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Search
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
     * @return Search
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
     * Set creator
     *
     * @param \Jam\UserBundle\Entity\User $creator
     * @return Search
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
     * Set distance
     *
     * @param integer $distance
     * @return Search
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set instruments
     *
     * @param string $instruments
     *
     * @return Search
     */
    public function setInstruments($instruments)
    {
        $this->instruments = $instruments;

        return $this;
    }

    /**
     * Get instruments
     *
     * @return string
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * Set isTeacher
     *
     * @param boolean $isTeacher
     *
     * @return Search
     */
    public function setIsTeacher($isTeacher)
    {
        $this->isTeacher = $isTeacher;

        return $this;
    }

    /**
     * Get isTeacher
     *
     * @return boolean
     */
    public function getIsTeacher()
    {
        return $this->isTeacher;
    }

    /**
     * Set isSubscribed
     *
     * @param boolean $isSubscribed
     *
     * @return Search
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }

    /**
     * Get isSubscribed
     *
     * @return boolean
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * Set genres
     *
     * @param string $genres
     *
     * @return Search
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * Get genres
     *
     * @return string
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set users
     *
     * @param array $users
     *
     * @return Search
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return array
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function getSortedIntegerUsers()
    {
        $results = array();

        if (is_array($this->users) && count($this->users) > 0) {
            foreach ($this->users as $userId) {
                $results[] = (int) $userId;
            }

            sort($results, SORT_NUMERIC);
        }

        return $results;
    }
}
