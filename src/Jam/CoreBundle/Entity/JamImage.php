<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jam\CoreBundle\Model\Image as BaseImage;

/**
 * AdImage
 *
 * @ORM\Table(name="jam_images")
 * @ORM\Entity
 */
class JamImage extends BaseImage
{
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="images")
     */
    private $jam;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $isDefault = false;

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     * @return Ad
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set user
     *
     * @param \Jam\CoreBundle\Entity\Jam $user
     * @return UserImage
     */
    public function setJam(\Jam\CoreBundle\Entity\Jam $jam = null)
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
}
