<?php

namespace Jam\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\Shout", mappedBy="creator", cascade={"all"} )
     */
    private $shouts;

    /**
     * @var collection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\Video", mappedBy="creator", cascade={"all"} )
     */
    private $videos;

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
     * @ORM\Column(type="boolean", length=1, nullable=false)
     *
     */
     private $isVisitor = false;

    /**
     * @ORM\Column(type="boolean", length=1, nullable=false)
     *
     */
     private $isJammer = false;

    /**
     * @ORM\Column(type="smallint", length=1, nullable=true)
     *
     */
    private $commitment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $hourlyRate;

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

    /**
     * Set brands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setBrands($brands)
    {
        $this->brands[] = $brands;

        return $this;
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

    /**
     * Add shouts
     *
     * @param \Jam\CoreBundle\Entity\Shout $shouts
     * @return User
     */
    public function addShout(\Jam\CoreBundle\Entity\Shout $shouts)
    {
        $this->shouts[] = $shouts;

        return $this;
    }

    /**
     * Remove shouts
     *
     * @param \Jam\CoreBundle\Entity\Shout $shouts
     */
    public function removeShout(\Jam\CoreBundle\Entity\Shout $shouts)
    {
        $this->shouts->removeElement($shouts);
    }

    /**
     * Get shouts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShouts()
    {
        return $this->shouts;
    }

    /**
     * Add videos
     *
     * @param \Jam\CoreBundle\Entity\Video $videos
     * @return User
     */
    public function addVideo(\Jam\CoreBundle\Entity\Video $videos)
    {
        $this->videos[] = $videos;

        return $this;
    }

    /**
     * Remove videos
     *
     * @param \Jam\CoreBundle\Entity\Video $videos
     */
    public function removeVideo(\Jam\CoreBundle\Entity\Video $videos)
    {
        $this->videos->removeElement($videos);
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

    public function getProfileFulfilment()
    {
        $percentage = 20;

        if ($this->brands->count() > 0){
            $percentage += 10;
        }

        if ($this->instruments->count() > 0){
            $percentage += 10;
        }

        if ($this->genres->count() > 0){
            $percentage += 20;
        }

        if ($this->artists->count() > 0){
            $percentage += 10;
        }

        if ($this->aboutMe != ''){
            $percentage += 10;
        }

        if ($this->location){
            $percentage += 20;
        }

        return $percentage;
    }

    /**
     * Set isVisitor
     *
     * @param boolean $isVisitor
     * @return User
     */
    public function setIsVisitor($isVisitor)
    {
        $this->isVisitor = $isVisitor;

        return $this;
    }

    /**
     * Get isVisitor
     *
     * @return boolean 
     */
    public function getIsVisitor()
    {
        return $this->isVisitor;
    }

    /**
     * Set isJammer
     *
     * @param boolean $isJammer
     * @return User
     */
    public function setIsJammer($isJammer)
    {
        $this->isJammer = $isJammer;

        return $this;
    }

    /**
     * Get isJammer
     *
     * @return boolean 
     */
    public function getIsJammer()
    {
        return $this->isJammer;
    }

    /**
     * Set hourlyRate
     *
     * @param string $hourlyRate
     * @return User
     */
    public function setHourlyRate($hourlyRate)
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    /**
     * Get hourlyRate
     *
     * @return string 
     */
    public function getHourlyRate()
    {
        return $this->hourlyRate;
    }

    /**
     * Set commitment
     *
     * @param integer $commitment
     *
     * @return User
     */
    public function setCommitment($commitment)
    {
        $this->commitment = $commitment;

        return $this;
    }

    /**
     * Get commitment
     *
     * @return integer
     */
    public function getCommitment()
    {
        return $this->commitment;
    }
}
