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
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Genre")
     * @ORM\JoinTable(
     *      name="search_genres",
     *      joinColumns={@ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $genres;

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
     * Constructor
     */
    public function __construct()
    {
        $this->genres = new ArrayCollection();
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
     * Add genres
     *
     * @param \Jam\CoreBundle\Entity\Genre $genres
     * @return Search
     */
    public function addGenre(\Jam\CoreBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \Jam\CoreBundle\Entity\Genre $genres
     */
    public function removeGenre(\Jam\CoreBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setGenres(ArrayCollection $genres)
    {
        $this->genres = $genres;
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
}
