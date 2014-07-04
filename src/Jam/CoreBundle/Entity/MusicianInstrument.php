<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Instrument
 *
 * @ORM\Table(name="musicians_instruments")
 * @ORM\Entity
 */
class MusicianInstrument
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="instruments")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=true)
     */
    private $musician;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Instrument", inversedBy="musicians")
     * @ORM\JoinColumn(name="instrument_id", referencedColumnName="id", nullable=true)
     */
    private $instrument;

    /**
     * @ORM\Column(type="smallint", length=1, nullable=true)
     *
     */
    private $skillLevel;


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
     * Set musician
     *
     * @param \Jam\UserBundle\Entity\User $musician
     * @return MusicianInstrument
     */
    public function setMusician(\Jam\UserBundle\Entity\User $musician = null)
    {
        $this->musician = $musician;

        return $this;
    }

    /**
     * Get musician
     *
     * @return \Jam\UserBundle\Entity\User 
     */
    public function getMusician()
    {
        return $this->musician;
    }

    /**
     * Set instrument
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instrument
     * @return MusicianInstrument
     */
    public function setInstrument(\Jam\CoreBundle\Entity\Instrument $instrument = null)
    {
        $this->instrument = $instrument;

        return $this;
    }

    /**
     * Get instrument
     *
     * @return \Jam\CoreBundle\Entity\Instrument 
     */
    public function getInstrument()
    {
        return $this->instrument;
    }

    /**
     * Set skillLevel
     *
     * @param integer $skillLevel
     * @return MusicianInstrument
     */
    public function setSkillLevel($skillLevel)
    {
        $this->skillLevel = $skillLevel;

        return $this;
    }

    /**
     * Get skillLevel
     *
     * @return integer 
     */
    public function getSkillLevel()
    {
        return $this->skillLevel;
    }
}
