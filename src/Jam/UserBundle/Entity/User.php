<?php

namespace Jam\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Jam\CoreBundle\Model\Image;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Jam\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */

    /** @ORM\Column(name="soundcloud_id", type="string", length=255, nullable=true) */
    protected $soundcloud_id;

    /** @ORM\Column(name="soundcloud_access_token", type="string", length=255, nullable=true) */
    protected $soundcloud_access_token;

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->jams = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Genre", inversedBy="musicians", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="musicians_genres",
     *      joinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $genres;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Artist", inversedBy="musicians", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="musicians_artists",
     *      joinColumns={@ORM\JoinColumn(name="artist_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $artists;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Brand", inversedBy="musicians", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="musicians_brands",
     *      joinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $brands;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Profile"}
     * )
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Profile"}
     * )
     */
    protected $lastName;

    /**
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\Jam", mappedBy="creator")
     */
    protected $jamsCreator;

    /**
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\JamMember", mappedBy="member", cascade={"persist"})
     */
    protected $jamsMember;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Jam", mappedBy="memberRequests", cascade={"persist"})
     */
    protected $jamsRequests;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $aboutMe;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\LocationBundle\Entity\Location", cascade={"all"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=true)
     */
    private $location;

    /**
     * @var collection
     *
     * @ORM\OneToMany(targetEntity="Jam\UserBundle\Entity\UserImage", mappedBy="user", cascade={"all"} )
     */
    private $images;

    /**
     * @var collection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\MusicianInstrument", mappedBy="musician", cascade={"all"} )
     */
    private $instruments;

    /**
     * @ORM\Column(type="smallint", length=1, nullable=true)
     *
     */
    private $gender;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     */
    private $birthDate;

    /**
     * @ORM\Column(type="boolean", length=1, nullable=false)
     *
     */
    private $isTeacher = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $avatar;

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
        foreach($jam->getJamMembers() AS $member){
            if($member->getMember()->getId()==$this->getId()){
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

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return User
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string 
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set location
     *
     * @param \Jam\LocationBundle\Entity\Location $location
     * @return User
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
     * {@inheritdoc}
     */
    public function hasImage(UserImage $image)
    {
        return $this->images->contains($image);
    }

    /**
     * Add images
     *
     * @param \Jam\CoreBundle\Model\Image $images
     * @return User
     */
    public function addImage(UserImage $image)
    {
        if (!$this->hasImage($image) && $image->getFile()!='') {
            $image->setUser($this);
            $this->images->add($image);
            $image->upload();
        }

        return $this;
    }

    /**
     * Add external path image
     *
     * @param \Jam\CoreBundle\Model\Image $images
     * @return User
     */
    public function addExternalImage(UserImage $image)
    {
        if (!$this->hasImage($image)) {
            $image->setUser($this);
            $this->images->add($image);
        }

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Jam\CoreBundle\Model\Image $images
     */
    public function removeImage(UserImage $image)
    {
        $image->setUser(null);
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add jamsMember
     *
     * @param \Jam\CoreBundle\Entity\JamMember $jamsMember
     * @return User
     */
    public function addJamsMember(\Jam\CoreBundle\Entity\JamMember $jamsMember)
    {
        $this->jamsMember[] = $jamsMember;

        return $this;
    }

    /**
     * Remove jamsMember
     *
     * @param \Jam\CoreBundle\Entity\JamMember $jamsMember
     */
    public function removeJamsMember(\Jam\CoreBundle\Entity\JamMember $jamsMember)
    {
        $this->jamsMember->removeElement($jamsMember);
    }

    /**
     * Get jamsMember
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJamsMember()
    {
        return $this->jamsMember;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGenderText()
    {
        if ($this->gender == null) return false;

        return $this->gender == 1 ? 'male' : 'female';
    }


    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Get age
     *
     * @return \DateTime
     */
    public function getAge()
    {
        if ($this->birthDate == null) return false;

        $now = new \DateTime();
        $diff = $now->diff($this->birthDate);

        return $diff->y;
    }

    /**
     * Add artists
     *
     * @param \Jam\CoreBundle\Entity\Artist $artists
     * @return User
     */
    public function addArtist(\Jam\CoreBundle\Entity\Artist $artists)
    {
        $this->artists[] = $artists;

        return $this;
    }

    /**
     * Remove artists
     *
     * @param \Jam\CoreBundle\Entity\Artist $artists
     */
    public function removeArtist(\Jam\CoreBundle\Entity\Artist $artists)
    {
        $this->artists->removeElement($artists);
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
     * Set artists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setArtists(ArrayCollection $artists)
    {
        $this->artists = $artists;
    }

    /**
     * Add brands
     *
     * @param \Jam\CoreBundle\Entity\Brand $brands
     * @return User
     */
    public function addBrand(\Jam\CoreBundle\Entity\Brand $brands)
    {
        $this->brands[] = $brands;

        return $this;
    }

    /**
     * Remove brands
     *
     * @param \Jam\CoreBundle\Entity\Brand $brands
     */
    public function removeBrand(\Jam\CoreBundle\Entity\Brand $brands)
    {
        $this->brands->removeElement($brands);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBrands()
    {
        return $this->brands;
    }

    public function getLat()
    {
        return $this->location ? $this->location->getLat() : false;
    }

    public function getLon()
    {
        return $this->location? $this->location->getLng() : false;
    }

    public function getPin()
    {
        if (!$this->getLat()) return null;

        return $this->getLat().','.$this->getLon();
    }

    public function getGenresNamesArray()
    {
        $genres = array();

        if ($this->genres->count()==0) $genres;

        foreach ($this->genres AS $g){
            array_push($genres, $g->getName());
        }

        return $genres;
    }

    public function getPlaceholderImage()
    {
        return '/images/placeholder-user.jpg';
    }

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set soundcloud_id
     *
     * @param string $soundcloudId
     * @return User
     */
    public function setSoundcloudId($soundcloudId)
    {
        $this->soundcloud_id = $soundcloudId;

        return $this;
    }

    /**
     * Get soundcloud_id
     *
     * @return string 
     */
    public function getSoundcloudId()
    {
        return $this->soundcloud_id;
    }

    /**
     * Set soundcloud_access_token
     *
     * @param string $soundcloudAccessToken
     * @return User
     */
    public function setSoundcloudAccessToken($soundcloudAccessToken)
    {
        $this->soundcloud_access_token = $soundcloudAccessToken;

        return $this;
    }

    /**
     * Get soundcloud_access_token
     *
     * @return string 
     */
    public function getSoundcloudAccessToken()
    {
        return $this->soundcloud_access_token;
    }

    /**
     * Set isTeacher
     *
     * @param boolean $isTeacher
     * @return User
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
     * Add instruments
     *
     * @param \Jam\CoreBundle\Entity\MusicianInstrument $instruments
     * @return User
     */
    public function addInstrument(\Jam\CoreBundle\Entity\MusicianInstrument $instruments)
    {
        $this->instruments[] = $instruments;

        return $this;
    }

    /**
     * Remove instruments
     *
     * @param \Jam\CoreBundle\Entity\MusicianInstrument $instruments
     */
    public function removeInstrument(\Jam\CoreBundle\Entity\MusicianInstrument $instruments)
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
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        if ($this->avatar){
            return 'uploads/avatars/'.$this->getId().'/'.$this->avatar;
        }else{
            return 'images/placeholder-user.jpg';
        }

    }
}
