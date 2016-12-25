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
     * @ORM\Column(name="type", type="smallint", )
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="stage", type="smallint")
     */
    private $stage;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = 1;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\Video", mappedBy="jam", cascade={"all"} )
     */
    private $videos;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\SoundcloudTrack", mappedBy="jam", cascade={"all"} )
     */
    private $soundcloudTracks;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="jams", cascade={"all"})
     * @ORM\JoinTable(
     *      name="jams_genres",
     *      joinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $genres;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Artist", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="jams_artists",
     *      joinColumns={@ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="artist_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $artists;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\JamMusicianInstrument", mappedBy="jam", cascade={"all"}, orphanRemoval=true )
     */
    private $members;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\JamInterest", mappedBy="jam", cascade={"all"}, orphanRemoval=true )
     */
    private $interests;

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
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Jam\CoreBundle\Entity\Genre $genre
     *
     * @return Jam
     */
    public function addGenre(\Jam\CoreBundle\Entity\Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre
     *
     * @param \Jam\CoreBundle\Entity\Genre $genre
     */
    public function removeGenre(\Jam\CoreBundle\Entity\Genre $genre)
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

    public function getGenresIds()
    {
        return $this->genres->map(function($genre) {
           return $genre->getId();
        })->toArray();
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
     * Add member
     *
     * @param \Jam\CoreBundle\Entity\JamMusicianInstrument $member
     *
     * @return Jam
     */
    public function addMember(\Jam\CoreBundle\Entity\JamMusicianInstrument $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \Jam\CoreBundle\Entity\JamMusicianInstrument $member
     */
    public function removeMember(\Jam\CoreBundle\Entity\JamMusicianInstrument $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    public function getExistingMembers()
    {
        return $this->members->filter(function($member) {
            if ($member->getMusician() || $member->getInvitee()) {
                return $member;
            }
        });
    }

    public function getMembersMusiciansIds()
    {
        $ids = array();
        foreach ($this->members AS $member){
            if ($member->getMusician()) {
                array_push($ids, $member->getMusician()->getId());
            }
        }
        return $ids;
    }

    public function getInterestedMusiciansIds()
    {
        $ids = array();
        foreach ($this->interests AS $interest){
            if ($interest->getMusician()) {
                array_push($ids, $interest->getMusician()->getId());
            }
        }
        return $ids;
    }

    /**
     * Set members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setMembers(ArrayCollection $members)
    {
        $this->members = $members;

        return $this;
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
     * Add video
     *
     * @param \Jam\CoreBundle\Entity\Video $video
     *
     * @return Jam
     */
    public function addVideo(\Jam\CoreBundle\Entity\Video $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video
     *
     * @param \Jam\CoreBundle\Entity\Video $video
     */
    public function removeVideo(\Jam\CoreBundle\Entity\Video $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }

    public function getInstruments()
    {
        return $this->members->filter(function($member) {
            if (!$member->getMusician() && !$member->getInvitee()) {
                return $member;
            }
        });
    }

    public function getInstrumentsIds()
    {
        return $this->getInstruments()->map(function($instrument) {
           return $instrument->getInstrumentId();
        })->toArray();
    }

    /**
     * Set stage
     *
     * @param string $stage
     *
     * @return Jam
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set status
     *
     * @param integer $status
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
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add interest
     *
     * @param \Jam\CoreBundle\Entity\JamInterest $interest
     *
     * @return Jam
     */
    public function addInterest(\Jam\CoreBundle\Entity\JamInterest $interest)
    {
        $this->interests[] = $interest;

        return $this;
    }

    /**
     * Remove interest
     *
     * @param \Jam\CoreBundle\Entity\JamInterest $interest
     */
    public function removeInterest(\Jam\CoreBundle\Entity\JamInterest $interest)
    {
        $this->interests->removeElement($interest);
    }

    /**
     * Get interests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Add soundcloudTrack
     *
     * @param \Jam\CoreBundle\Entity\SoundcloudTrack $soundcloudTrack
     *
     * @return Jam
     */
    public function addSoundcloudTrack(\Jam\CoreBundle\Entity\SoundcloudTrack $soundcloudTrack)
    {
        $this->soundcloudTracks[] = $soundcloudTrack;

        return $this;
    }

    /**
     * Remove soundcloudTrack
     *
     * @param \Jam\CoreBundle\Entity\SoundcloudTrack $soundcloudTrack
     */
    public function removeSoundcloudTrack(\Jam\CoreBundle\Entity\SoundcloudTrack $soundcloudTrack)
    {
        $this->soundcloudTracks->removeElement($soundcloudTrack);
    }

    /**
     * Get soundcloudTracks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSoundcloudTracks()
    {
        return $this->soundcloudTracks;
    }
}
