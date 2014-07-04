<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Instrument
 *
 * @ORM\Table(name="instruments")
 * @ORM\Entity
 */
class Instrument
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
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\MusicianInstrument", mappedBy="instrument" )
     */
    private $musicians;

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
        $this->musicians = new \Doctrine\Common\Collections\ArrayCollection();
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
}
