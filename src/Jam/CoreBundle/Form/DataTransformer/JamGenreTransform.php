<?php
namespace Jam\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Jam\CoreBundle\Entity\JamGenre;
use Jam\CoreBundle\Entity\MusicianGenre;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class JamGenreTransform implements DataTransformerInterface
{
    protected $entityManager;

    protected $jam;

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
    public function transform($genres)
    {
        if (null === $genres) {
            return "";
        }

        $genre = '';
        foreach ($genres AS $k => $a){
            if($k!=0) $genre .= ',';
            $genre .= $a->getGenre()->getId();

        }

        return $genre;
    }

    /**
     * Transforms a string (number) to an object (group).
     *
     * @param  string $number
     * @return Group|null
     * @throws TransformationFailedException if object (group) is not found.
     */
    public function reverseTransform($ids)
    {
        if (is_array($ids)){
            return implode(",", $ids);
        }

        $genresCollection = new ArrayCollection();

        if (!$ids || $ids == '') {
            return $genresCollection;
        }

        $genres = explode(",", $ids);

        foreach($genres AS $k=>$id){

            $musicianGenre = new JamGenre();

            $genre = $this->entityManager
                ->getRepository('JamCoreBundle:Genre')
                ->find($id);

            if (null === $genre) {
                throw new TransformationFailedException(sprintf(
                    'Genre with ID "%s" does not exist!',
                    $id
                ));
            }

            $musicianGenre->setGenre($genre);
            $musicianGenre->setPosition($k);
            $musicianGenre->setJam($this->jam);

            $this->entityManager->persist($musicianGenre);

            $genresCollection->add($musicianGenre);
        }

        return $genresCollection;
    }

    public function setMydata($data)
    {
        $this->jam = $data;
    }
}