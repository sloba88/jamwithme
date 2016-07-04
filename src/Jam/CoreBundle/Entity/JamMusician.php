<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * JamMusician
 *
 * @ORM\Table(name="jams_musicians")
 * @ORM\Entity
 */
class JamMusician
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
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="jams")
     * @ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)
     */
    private $jam;

    /**
     * @var User $musician
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="instruments")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)
     */
    private $musician;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Instrument", inversedBy="musicians")
     * @ORM\JoinColumn(name="instrument_id", referencedColumnName="id", nullable=false)
     */
    private $instruments;

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
     * @return JamMusician
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
     * Set musician
     *
     * @param \Jam\UserBundle\Entity\User $musician
     *
     * @return JamMusician
     */
    public function setMusician(\Jam\UserBundle\Entity\User $musician)
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
     * Set instruments
     *
     * @param \Jam\CoreBundle\Entity\Instrument $instruments
     *
     * @return JamMusician
     */
    public function setInstruments(\Jam\CoreBundle\Entity\Instrument $instruments)
    {
        $this->instruments = $instruments;

        return $this;
    }

    /**
     * Get instruments
     *
     * @return \Jam\CoreBundle\Entity\Instrument
     */
    public function getInstruments()
    {
        return $this->instruments;
    }
}
