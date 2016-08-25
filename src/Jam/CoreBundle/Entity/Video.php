<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Video
 *
 * @ORM\Table(name="videos")
 * @ORM\Entity
 */
class Video
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
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="videos")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
     */
    private $creator;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="videos")
     * @ORM\JoinColumn(name="jam_id", referencedColumnName="id", nullable=true)
     */
    private $jam;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     */
    protected $url;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     */
    protected $type = 0;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;


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
     * Set url
     *
     * @param string $url
     *
     * @return Video
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Video
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Video
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set creator
     *
     * @param \Jam\UserBundle\Entity\User $creator
     *
     * @return Video
     */
    public function setCreator(\Jam\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Jam\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set jam
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     *
     * @return Video
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

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Video
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

    public function getThumbnail()
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->url, $match)) {
            $video_id = $match[1];
            return 'https://img.youtube.com/vi/' . $video_id . '/0.jpg';
        }
    }
}
