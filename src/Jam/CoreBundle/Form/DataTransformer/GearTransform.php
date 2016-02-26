<?php
namespace Jam\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Jam\CoreBundle\Entity\MusicianGear;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class GearTransform implements DataTransformerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (group) to a string (number).
     *
     * @param  Group|null $group
     * @return string
     */
    public function transform($gears)
    {
        if (null === $gears) {
            return "";
        }
        $gear = '';
        foreach ($gears AS $k => $a){
            if($k!=0) $gear .= ',';
            $gear .= $a->getName();

        }

        return $gear;
    }

    /**
     * Transforms a string (number) to an object (group).
     *
     * @param  string $number
     * @return Group|null
     * @throws TransformationFailedException if object (group) is not found.
     */
    public function reverseTransform($name)
    {
        $gearCollection = new ArrayCollection();

        if (!$name || $name == '') {
            return $gearCollection;
        }

        $names = explode(",", $name);

        foreach($names AS $k=>$name){

            $musicianGear = new MusicianGear();

            $musicianGear->setPosition($k);
            $musicianGear->setName($name);
            $this->entityManager->persist($musicianGear);

            $gearCollection->add($musicianGear);
        }

        return $gearCollection;
    }
}