<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Instrument
 *
 * @ORM\Table(name="musicians_genres")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 */
class MusicianGenre
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
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="genres")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)
     */
    private $musician;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Genre")
     * @ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)
     */
    private $genre;

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
     * @return MusicianGenre
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
     * @return MusicianGenre
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
     * Set genre
     *
     * @param \Jam\CoreBundle\Entity\Genre $genre
     *
     * @return MusicianGenre
     */
    public function setGenre(\Jam\CoreBundle\Entity\Genre $genre = null)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return \Jam\CoreBundle\Entity\Genre
     */
    public function getGenre()
    {
        return $this->genre;
    }
}
