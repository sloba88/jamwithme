<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Jam
 *
 * @ORM\Table(name="jams")
 * @ORM\Entity
 */
class Jam
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=100)
     */
    private $status;

    /**
     * @var collection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\JamGenre", mappedBy="jam", cascade={"all"}, orphanRemoval=true )
     */
    private $genres;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Artist", inversedBy="jams", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="jams_artists",
     *      joinColumns={@ORM\JoinColumn(name="artist_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $artists;

    /**
     * @var collection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\JamInstrument", mappedBy="jam", cascade={"all"}, orphanRemoval=true )
     */
    private $instruments;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\UserBundle\Entity\User", mappedBy="artists" )
     */
    private $musicians;

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
     * @var integer
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
     */
    private $creator;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\LocationBundle\Entity\Location", cascade={"all"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=true)
     */
    private $location;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->artists = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instruments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->musicians = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Jam
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Jam
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
     * Set type
     *
     * @param string $type
     *
     * @return Jam
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
     * Set status
     *
     * @param string $status
     *
     * @return Jam
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Jam
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
     *
     * @return Jam
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
     * Add genre
     *
     * @param \Jam\CoreBundle\Entity\JamGenre $genre
     *
     * @return Jam
     */
    public function addGenre(\Jam\CoreBundle\Entity\JamGenre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre
     *
     * @param \Jam\CoreBundle\Entity\JamGenre $genre
     */
    public function removeGenre(\Jam\CoreBundle\Entity\JamGenre $genre)
    {
        $this->genres->removeElement($genre);
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
     * Add artist
     *
     * @param \Jam\CoreBundle\Entity\Artist $artist
     *
     * @return Jam
     */
    public function addArtist(\Jam\CoreBundle\Entity\Artist $artist)
    {
        $this->artists[] = $artist;

        return $this;
    }

    /**
     * Remove artist
     *
     * @param \Jam\CoreBundle\Entity\Artist $artist
     */
    public function removeArtist(\Jam\CoreBundle\Entity\Artist $artist)
    {
        $this->artists->removeElement($artist);
    }

    /**
     * Get artists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArtists()
    {
        return $this->artists;
    }

    /**
     * Add instrument
     *
     * @param \Jam\CoreBundle\Entity\JamInstrument $instrument
     *
     * @return Jam
     */
    public function addInstrument(\Jam\CoreBundle\Entity\JamInstrument $instrument)
    {
        $this->instruments[] = $instrument;

        return $this;
    }

    /**
     * Remove instrument
     *
     * @param \Jam\CoreBundle\Entity\JamInstrument $instrument
     */
    public function removeInstrument(\Jam\CoreBundle\Entity\JamInstrument $instrument)
    {
        $this->instruments->removeElement($instrument);
    }

    /**
     * Get instruments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * Add musician
     *
     * @param \Jam\UserBundle\Entity\User $musician
     *
     * @return Jam
     */
    public function addMusician(\Jam\UserBundle\Entity\User $musician)
    {
        $this->musicians[] = $musician;

        return $this;
    }

    /**
     * Remove musician
     *
     * @param \Jam\UserBundle\Entity\User $musician
     */
    public function removeMusician(\Jam\UserBundle\Entity\User $musician)
    {
        $this->musicians->removeElement($musician);
    }

    /**
     * Get musicians
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMusicians()
    {
        return $this->musicians;
    }

    /**
     * Set creator
     *
     * @param \Jam\UserBundle\Entity\User $creator
     *
     * @return Jam
     */
    public function setCreator(\Jam\UserBundle\Entity\User $creator)
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
     * Set location
     *
     * @param \Jam\LocationBundle\Entity\Location $location
     *
     * @return Jam
     */
    public function setLocation(\Jam\LocationBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \Jam\LocationBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Jam
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
