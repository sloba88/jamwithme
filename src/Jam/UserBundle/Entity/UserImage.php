<?php

namespace Jam\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jam\CoreBundle\Model\Image as BaseImage;

/**
 * AdImage
 *
 * @ORM\Table(name="user_images")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UserImage extends BaseImage
{
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="images")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * Set user
     *
     * @param \Jam\UserBundle\Entity\User $user
     * @return UserImage
     */
    public function setUser(\Jam\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Jam\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return UserImage
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images/'.$this->user->getId();
    }
}
