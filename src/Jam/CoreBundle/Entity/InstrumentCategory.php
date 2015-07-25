<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Instrument
 *
 * @ORM\Table(name="instrument_categories")
 * @ORM\Entity
 */
class InstrumentCategory
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
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\Instrument", mappedBy="instrument" )
     */
    private $instruments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instruments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return InstrumentCategory
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
     * Add instrument
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instrument
     *
     * @return InstrumentCategory
     */
    public function addInstrument(\Jam\CoreBundle\Entity\Instrument $instrument)
    {
        $this->instruments[] = $instrument;

        return $this;
    }

    /**
     * Remove instrument
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instrument
     */
    public function removeInstrument(\Jam\CoreBundle\Entity\Instrument $instrument)
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
}
