<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * JamGenre
 *
 * @ORM\Table(name="jams_genres")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 */
class JamGenre
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
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="genres", cascade={"persist"})
     * @ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=false)
     */
    private $jam;

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
     * @return JamGenre
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
     * Set jam
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     *
     * @return JamGenre
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
     * Set genre
     *
     * @param \Jam\CoreBundle\Entity\Genre $genre
     *
     * @return JamGenre
     */
    public function setGenre(\Jam\CoreBundle\Entity\Genre $genre)
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
