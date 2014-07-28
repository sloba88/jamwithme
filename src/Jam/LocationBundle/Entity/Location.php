<?php

namespace Jam\LocationBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
 
/**
 * Jam\LocationBundle\Entity
 *
 * @ORM\Table(name="locations")
 * @ORM\Entity(repositoryClass="Jam\LocationBundle\Entity\LocationRepository")
 */
class Location
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
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255, nullable=true)
     */
    protected $locality;

    /**
     * @var string
     *
     * @ORM\Column(name="neighborhood", type="string", length=255, nullable=true)
     */
    protected $neighborhood;
 
    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=255, nullable=true)
     */
    protected $route;

    /**
     * @var integer
     *
     * @ORM\Column(name="zip", type="string", length=10, nullable=true)
     */
    protected $zip;
 
    /**
     * @var float     Latitude of the position
     *
     * @ORM\Column(name="lat", type="float", nullable=true)
     */
    protected $lat;
 
    /**
     * @var float     Longitude of the position
     *
     * @ORM\Column(name="lng", type="float", nullable=true)
     */
    protected $lng;

    /**
     * @var string
     *
     * @ORM\Column(name="administrative_area_level_3", type="string", length=255, nullable=true)
     */
    protected $administrative_area_level_3;
    

    public function setAddress($address)
    {
        $this->address = $address;
    }
 
    public function getAddress()
    {
        return $this->address;
    }
 
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }
 
    public function getLocality()
    {
        return $this->locality;
    }
 
    public function setCountry($country)
    {
        $this->country = $country;
    }
 
    public function getCountry()
    {
        return $this->country;
    }
 
    public function getLat()
    {
        return $this->lat;
    }
 
    public function setLat($lat)
    {
        if (is_string($lat)) {
            $lat = floatval($lat);
        }
        $this->lat = $lat;
    }
 
    public function getLng()
    {
        return $this->lng;
    }
 
    public function setLng($lng)
    {
        if (is_string($lng)) {
            $lng = floatval($lng);
        }
        $this->lng = $lng;
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
     * Set zip
     *
     * @param integer $zip
     * @return Location
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return integer 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Location
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set administrative_area_level_3
     *
     * @param string $administrativeAreaLevel3
     * @return Location
     */
    public function setAdministrativeAreaLevel3($administrativeAreaLevel3)
    {
        $this->administrative_area_level_3 = $administrativeAreaLevel3;

        return $this;
    }

    /**
     * Get administrative_area_level_3
     *
     * @return string 
     */
    public function getAdministrativeAreaLevel3()
    {
        return $this->administrative_area_level_3;
    }

    /**
     * Set neighborhood
     *
     * @param string $neighborhood
     * @return Location
     */
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * Get neighborhood
     *
     * @return string 
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }
}
