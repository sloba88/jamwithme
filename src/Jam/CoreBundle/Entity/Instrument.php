<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Instrument
 *
 * @ORM\Table(name="instruments")
 * @ORM\Entity
 * @Gedmo\TranslationEntity(class="InstrumentTranslation")
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
     * @Gedmo\Translatable
     */
    private $name;

    /**
     * @var collection
     *
     * @ORM\OneToMany(targetEntity="Jam\CoreBundle\Entity\MusicianInstrument", mappedBy="instrument" )
     */
    private $musicians;

    /**
     * @ORM\ManyToOne(targetEntity="InstrumentCategory", inversedBy="instruments", cascade={"persist"})
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     **/
    private $category;

    /**
     * @ORM\OneToMany(
     *   targetEntity="InstrumentTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->musicians = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set category
     *
     * @param \Jam\CoreBundle\Entity\InstrumentCategory $category
     *
     * @return Instrument
     */
    public function setCategory(\Jam\CoreBundle\Entity\InstrumentCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Jam\CoreBundle\Entity\InstrumentCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(InstrumentTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }
}
