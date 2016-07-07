<?php
namespace Jam\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Jam\CoreBundle\Entity\Artist;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArtistTransform implements DataTransformerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (group) to a string (number).
     *
     * @param  String|null $group
     * @return string
     */
    public function transform($artist)
    {
        if (null === $artist) {
            return "";
        }

        $artists = '';
        foreach ($artist AS $k => $a){
            if($k!=0) $artists .= ',';
            $artists .= $a->getName();

        }

        return $artists;
    }

    /**
     * Transforms a string (number) to an object (group).
     *
     * @param  array $artists
     * @return ArrayCollection
     * @throws TransformationFailedException if object (group) is not found.
     */
    public function reverseTransform($artists)
    {
        $artistsCollection = new ArrayCollection();

        if (null === $artists) {
            return $artistsCollection;
        }

        foreach($artists AS $name){

            $artist = $this->entityManager
                ->getRepository('JamCoreBundle:Artist')
                ->findOneBy(array('name' => $name));

            if (null === $artist){
                $artist = new Artist();
                $artist->setName($name);
            }

            $artistsCollection->add($artist);
        }

        return $artistsCollection;
    }
}