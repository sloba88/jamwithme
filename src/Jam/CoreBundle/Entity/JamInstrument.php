<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * JamInstrument
 *
 * @ORM\Table(name="jams_instruments")
 * @ORM\Entity
 */
class JamInstrument
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
     * @var User $musician
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="instruments")
     * @ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)
     */
    private $jam;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Instrument")
     * @ORM\JoinColumn(name="instrument_id", referencedColumnName="id", nullable=false)
     */
    private $instrument;


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
     * Set jam
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     *
     * @return JamInstrument
     */
    public function setJam(\Jam\CoreBundle\Entity\Jam $jam)
    {
        $this->jam = $jam;

        return $this;
    }

    /**
     * Get jam
     *
     * @return \Jam\CoreBundle\Entity\Jam
     */
    public function getJam()
    {
        return $this->jam;
    }

    /**
     * Set instrument
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instrument
     *
     * @return JamInstrument
     */
    public function setInstrument(\Jam\CoreBundle\Entity\Instrument $instrument)
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
}
