<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Genre
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
     * @ORM\ManyToOne(targetEntity="GenreCategory", inversedBy="genres", cascade={"persist"})
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     **/
    private $category;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Jam", mappedBy="genres" )
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
     * @return Genre
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
     * Set category
     *
     * @param \Jam\CoreBundle\Entity\GenreCategory $category
     *
     * @return Genre
     */
    public function setCategory(\Jam\CoreBundle\Entity\GenreCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Jam\CoreBundle\Entity\GenreCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jams = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add jam
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     *
     * @return Genre
     */
    public function addJam(\Jam\CoreBundle\Entity\Jam $jam)
    {
        $this->jams[] = $jam;

        return $this;
    }

    /**
     * Remove jam
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     */
    public function removeJam(\Jam\CoreBundle\Entity\Jam $jam)
    {
        $this->jams->removeElement($jam);
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
