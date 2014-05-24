<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Jam
 * @ORM\Table(name="jam_members",uniqueConstraints={@UniqueConstraint(name="jam_member_unique", columns={"jam_id", "user_id"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"member"})
 * *
 */
class JamMember
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
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User", inversedBy="jamsMember")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Jam\CoreBundle\Entity\Jam", inversedBy="jamMembers")
     * @ORM\JoinColumn(name="jam_id", referencedColumnName="id")
     */
    private $jam;


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
     * Set member
     *
     * @param \Jam\UserBundle\Entity\User $member
     * @return JamMember
     */
    public function setMember(\Jam\UserBundle\Entity\User $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \Jam\UserBundle\Entity\User 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set jam
     *
     * @param \Jam\CoreBundle\Entity\Jam $jam
     * @return JamMember
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
