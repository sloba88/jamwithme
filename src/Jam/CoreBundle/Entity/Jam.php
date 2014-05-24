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
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=100)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="membersCount", type="integer")
     */
    private $membersCount;

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
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\JamMember", mappedBy="jam")
     */
    private $jamMembers;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\UserBundle\Entity\User", inversedBy="jamsRequests" )
     * @ORM\JoinTable(
     *      name="jam_member_requests",
     *      joinColumns={@ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $memberRequests;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Genre", inversedBy="jams", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="jam_genres",
     *      joinColumns={@ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $genres;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=1000)
     */
    private $description;

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
        $this->jamMembers = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
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
     * Set membersCount
     *
     * @param integer $membersCount
     * @return Jam
     */
    public function setMembersCount($membersCount)
    {
        $this->membersCount = $membersCount;

        return $this;
    }

    /**
     * Get membersCount
     *
     * @return integer 
     */
    public function getMembersCount()
    {
        return $this->membersCount;
    }

    /**
     * Set creator
     *
     * @param \Jam\UserBundle\Entity\User $creator
     * @return Jam
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
     * Add memberRequests
     *
     * @param \Jam\UserBundle\Entity\User $memberRequests
     * @return Jam
     */
    public function addMemberRequest(\Jam\UserBundle\Entity\User $memberRequests)
    {
        $this->memberRequests[] = $memberRequests;

        return $this;
    }

    /**
     * Remove memberRequests
     *
     * @param \Jam\UserBundle\Entity\User $memberRequests
     */
    public function removeMemberRequest(\Jam\UserBundle\Entity\User $memberRequests)
    {
        $this->memberRequests->removeElement($memberRequests);
    }

    /**
     * Get memberRequests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberRequests()
    {
        return $this->memberRequests;
    }

    /**
     * Add genres
     *
     * @param \Jam\CoreBundle\Entity\Genre $genres
     * @return Jam
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
     * Set description
     *
     * @param string $description
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
     * Set slug
     *
     * @param string $slug
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
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
     * Add jamMembers
     *
     * @param \Jam\CoreBundle\Entity\JamMember $jamMembers
     * @return Jam
     */
    public function addJamMember(\Jam\CoreBundle\Entity\JamMember $jamMembers)
    {
        $this->jamMembers[] = $jamMembers;

        return $this;
    }

    /**
     * Remove jamMembers
     *
     * @param \Jam\CoreBundle\Entity\JamMember $jamMembers
     */
    public function removeJamMember(\Jam\CoreBundle\Entity\JamMember $jamMembers)
    {
        $this->jamMembers->removeElement($jamMembers);
    }

    /**
     * Get jamMembers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJamMembers()
    {
        return $this->jamMembers;
    }
}
