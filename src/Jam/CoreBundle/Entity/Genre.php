<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Instrument
 *
 * @ORM\Table(name="genres")
 * @ORM\Entity
 */
class Genre
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
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\UserBundle\Entity\User", mappedBy="genres" )
     */
    private $musicians;

    /**
     * @var collection
     *
     * @ORM\ManyToMany(targetEntity="Jam\CoreBundle\Entity\Jam", mappedBy="genres")
     */
    private $jams;

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
     * @return Instrument
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
     * Constructor
     */
    public function __construct()
    {
        $this->musicians = new ArrayCollection();
        $this->jams = new ArrayCollection();
    }

    /**
     * Add musicians
     *
     * @param \Jam\UserBundle\Entity\User $musicians
     * @return Instrument
     */
    public function addMusician(\Jam\UserBundle\Entity\User $musicians)
    {
        $this->musicians[] = $musicians;

        return $this;
    }

    /**
     * Remove musicians
     *
     * @param \Jam\UserBundle\Entity\User $musicians
     */
    public function removeMusician(\Jam\UserBundle\Entity\User $musicians)
    {
        $this->musicians->removeElement($musicians);
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
     * Add jams
     *
     * @param \Jam\CoreBundle\Entity\Jam $jams
     * @return Genre
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
}
