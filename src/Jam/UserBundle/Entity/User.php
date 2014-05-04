<?php

namespace Jam\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->jams = new ArrayCollection();
    }

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Instrument", inversedBy="musicians", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="musicians_instruments",
     *      joinColumns={@ORM\JoinColumn(name="instrument_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $instruments;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Genre", inversedBy="musicians", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="musicians_genres",
     *      joinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $genres;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $lastName;

    /**
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\Jam", mappedBy="creator")
     */
    protected $jamsCreator;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Jam", mappedBy="members", cascade={"persist"})
     */
    protected $jams;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Jam", mappedBy="memberRequests", cascade={"persist"})
     */
    protected $jamsRequests;

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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Add instruments
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instruments
     * @return User
     */
    public function addInstrument(\Jam\CoreBundle\Entity\Instrument $instruments)
    {
        $this->instruments[] = $instruments;

        return $this;
    }

    /**
     * Remove instruments
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instruments
     */
    public function removeInstrument(\Jam\CoreBundle\Entity\Instrument $instruments)
    {
        $this->instruments->removeElement($instruments);
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
     * Add genres
     *
     * @param \Jam\CoreBundle\Entity\Genre $genres
     * @return User
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
     * Add jams
     *
     * @param \Jam\CoreBundle\Entity\Jam $jams
     * @return User
     */
    public function addJam(\Jam\CoreBundle\Entity\Jam $jams)
    {
        $this->jams[] = $jams;

        return $this;
    }

    /**
     * Remove jams
     *
     * @param \Jam\CoreBundle\Entity\Jam $jams
     */
    public function removeJam(\Jam\CoreBundle\Entity\Jam $jams)
    {
        $this->jams->removeElement($jams);
    }

    /**
     * Get jams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJams()
    {
        return $this->jams;
    }

    /**
     * Check if member
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     * @return User
     */
    public function isJamMember(\Jam\CoreBundle\Entity\Jam $jam)
    {
        foreach($jam->getMembers() AS $member){
            if($member->getId()==$this->getId()){
                return true;
            }
        }

        return false;
    }

    /**
     * Check if requested member
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     * @return User
     */
    public function isJamMemberRequested(\Jam\CoreBundle\Entity\Jam $jam)
    {
        foreach($jam->getMemberRequests() AS $request){
            if($request->getId()==$this->getId()){
                return true;
            }
        }

        return false;
    }

    /**
     * Add jamsCreator
     *
     * @param \Jam\CoreBundle\Entity\Jam $jamsCreator
     * @return User
     */
    public function addJamsCreator(\Jam\CoreBundle\Entity\Jam $jamsCreator)
    {
        $this->jamsCreator[] = $jamsCreator;

        return $this;
    }

    /**
     * Remove jamsCreator
     *
     * @param \Jam\CoreBundle\Entity\Jam $jamsCreator
     */
    public function removeJamsCreator(\Jam\CoreBundle\Entity\Jam $jamsCreator)
    {
        $this->jamsCreator->removeElement($jamsCreator);
    }

    /**
     * Get jamsCreator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJamsCreator()
    {
        return $this->jamsCreator;
    }

    /**
     * Add jamsRequests
     *
     * @param \Jam\CoreBundle\Entity\Jam $jamsRequests
     * @return User
     */
    public function addJamsRequest(\Jam\CoreBundle\Entity\Jam $jamsRequests)
    {
        $this->jamsRequests[] = $jamsRequests;

        return $this;
    }

    /**
     * Remove jamsRequests
     *
     * @param \Jam\CoreBundle\Entity\Jam $jamsRequests
     */
    public function removeJamsRequest(\Jam\CoreBundle\Entity\Jam $jamsRequests)
    {
        $this->jamsRequests->removeElement($jamsRequests);
    }

    /**
     * Get jamsRequests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJamsRequests()
    {
        return $this->jamsRequests;
    }
}
