<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Instrument
 *
 * @ORM\Table(name="musicians_gear")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 */
class MusicianGear
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
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="gear")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)
     */
    private $musician;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


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
     * Set position
     *
     * @param integer $position
     *
     * @return MusicianGear
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set musician
     *
     * @param \Jam\UserBundle\Entity\User $musician
     *
     * @return MusicianGear
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
     * Set name
     *
     * @param string $name
     *
     * @return MusicianGear
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
}
