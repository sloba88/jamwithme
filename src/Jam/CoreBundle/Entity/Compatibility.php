<?php

namespace Jam\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Compatibility
 *
 * @ORM\Table(name="compatibility")
 * @ORM\Entity
 */
class Compatibility
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
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", nullable=false)
     */
    private $musician;

    /**
     * @var User $musician
     *
     * @ORM\ManyToOne(targetEntity="Jam\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="musician2_id", referencedColumnName="id", nullable=false)
     */
    private $musician2;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     */
    protected $value;


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
     * Set value
     *
     * @param integer $value
     *
     * @return Compatibility
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set musician
     *
     * @param \Jam\UserBundle\Entity\User $musician
     *
     * @return Compatibility
     */
    public function setMusician(\Jam\UserBundle\Entity\User $musician)
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
     * Set musician2
     *
     * @param \Jam\UserBundle\Entity\User $musician2
     *
     * @return Compatibility
     */
    public function setMusician2(\Jam\UserBundle\Entity\User $musician2)
    {
        $this->musician2 = $musician2;

        return $this;
    }

    /**
     * Get musician2
     *
     * @return \Jam\UserBundle\Entity\User
     */
    public function getMusician2()
    {
        return $this->musician2;
    }

    public function calculate()
    {
        /* @var $user \Jam\UserBundle\Entity\User */
        /* @var $me \Jam\UserBundle\Entity\User */
        $user = $this->musician2;
        $me = $this->musician;

        $compatibility = 0;
        $possibleMatches = 0;
        $totalMatches = 0;

        $artistIndex = 8;
        $genresIndex = 5;
        $ageIndex = 3;
        $commitmentIndex = 5;

        /* calculate artists */
        $matchedIndexes = array();
        foreach ($user->getArtists() AS $k1 => $v1){
            foreach ($me->getArtists() AS $k2 => $v2){
                if (in_array($k2, $matchedIndexes)) continue;
                if ($v1->getId() == $v2->getId()){
                    $compatibility += $artistIndex;
                    $totalMatches ++;
                    //if matched skip it next time
                    array_push($matchedIndexes, $k2);
                }else{
                    //$compatibility -= 2;
                }
            }
        }

        if ($user->getArtists()) {
            $userArtistCount = $user->getArtists()->count();
        } else {
            $userArtistCount = 0;
        }

        if ($me->getArtists()) {
            $meArtistCount = $me->getArtists()->count();
        } else {
            $meArtistCount = 0;
        }

        $possibleMatches += min($userArtistCount, $meArtistCount) * $artistIndex;

        /* calculate genres */
        $matchedIndexes = array();
        foreach ($user->getGenres() AS $k1 => $v1){
            foreach ($me->getGenres() AS $k2 => $v2){
                if (in_array($k2, $matchedIndexes)) continue;
                if ($v1->getGenre()->getId() == $v2->getGenre()->getId()){
                    $compatibility += $genresIndex;
                    $totalMatches ++;
                    //if matched skip it next time
                    array_push($matchedIndexes, $k2);
                }else{
                    //$compatibility -= 2;
                }
            }
        }

        if ($user->getGenres()) {
            $userGenresCount = $user->getGenres()->count();
        } else {
            $userGenresCount = 0;
        }

        if ($me->getGenres()) {
            $meGenresCount = $me->getGenres()->count();
        } else {
            $meGenresCount = 0;
        }

        $possibleMatches += min($userGenresCount, $meGenresCount) * $genresIndex;

        /* calculate availability (commitment) */
        if ($user->getCommitment() && $me->getCommitment()){
            $commitmentDiff = abs(intval($user->getCommitment()) - intval($me->getCommitment()));
            if ($commitmentDiff < 2){
                $compatibility += $commitmentIndex;
                $totalMatches ++;
            }
        }

        $possibleMatches += $commitmentIndex;

        /* calculate age */
        if ($user->getAge() && $me->getAge()){
            $ageDiff = abs(intval($user->getAge()) - intval($me->getAge()));
            if ($ageDiff < 5){
                $compatibility += $ageIndex;
                $totalMatches ++;
            }
        }

        $possibleMatches += $ageIndex;

        $matchesIndex = (100 / $possibleMatches) * $compatibility;

        if ($matchesIndex < 20) {
            $matchesIndex = rand(15, 25);
        }

        $this->value = intval($matchesIndex);
    }

    public function isEnabled()
    {
        if (!$this->getMusician()->isEnabled() || !$this->getMusician2()->isEnabled()) {
            return false;
        }

        return true;
    }
}
