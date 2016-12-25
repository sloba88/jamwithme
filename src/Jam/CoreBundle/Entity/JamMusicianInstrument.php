<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Jam\UserBundle\Entity\User;

/**
 * JamMusicianInstrument
 *
 * @ORM\Table(name="jams_musicians_instruments")
 * @ORM\Entity
 */
class JamMusicianInstrument
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
     * @var Jam $jam
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="members")
     * @ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)
     */
    private $jam;

    /**
     * @var User $musician
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="jams")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=true)
     */
    private $musician;

    /**
     * @var Instrument $instrument
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Instrument")
     * @ORM\JoinColumn(name="instrument_id", referencedColumnName="id", nullable=false)
     */
    private $instrument;

    /**
     * @var User $musician
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\Invitation", cascade={"persist"})
     * @ORM\JoinColumn(name="invitation_id", referencedColumnName="id", nullable=true)
     */
    private $invitee;

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
     * @return JamMusicianInstrument
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
     * @return JamMusicianInstrument
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
     *
     * @return JamMusicianInstrument
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

    /**
     * Set invitee
     *
     * @param \Jam\UserBundle\Entity\Invitation $invitee
     *
     * @return JamMusicianInstrument
     */
    public function setInvitee(\Jam\UserBundle\Entity\Invitation $invitee = null)
    {
        $this->invitee = $invitee;

        return $this;
    }

    /**
     * Get invitee
     *
     * @return \Jam\UserBundle\Entity\Invitation
     */
    public function getInvitee()
    {
        return $this->invitee;
    }

    public function getInstrumentId()
    {
        return $this->instrument->getId();
    }
}
