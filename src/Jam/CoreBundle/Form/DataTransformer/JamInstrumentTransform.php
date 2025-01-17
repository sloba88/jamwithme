<?php
namespace Jam\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Jam\CoreBundle\Entity\JamGenre;
use Jam\CoreBundle\Entity\JamInstrument;
use Jam\CoreBundle\Entity\JamMusicianInstrument;
use Jam\CoreBundle\Entity\MusicianGenre;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class JamInstrumentTransform implements DataTransformerInterface
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
    public function transform($members)
    {
        if (null === $members) {
            return array();
        }

        $instruments = array();

        foreach ($members AS $i) {
            array_push($instruments, $i->getInstrument());
        }

        return $instruments;
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
        $musicianInstrumentCollection = array();

        if (!$ids || $ids == '') {
            return $musicianInstrumentCollection;
        }

        //first clear all the instruments

        foreach($ids AS $k=>$id){

            $mInstrument = $this->entityManager
                ->getRepository('JamCoreBundle:JamMusicianInstrument')
                ->findOneBy(array('instrument' => $id, 'musician' => null, 'jam' => $this->jam));

            if ($mInstrument) {
                $this->entityManager->remove($mInstrument);
            }

            $jamInstrument = new JamMusicianInstrument();

            $instrument = $this->entityManager
                ->getRepository('JamCoreBundle:Instrument')
                ->find($id);

            if (null === $instrument) {
                throw new TransformationFailedException(sprintf(
                    'Instrument with ID "%s" does not exist!',
                    $id
                ));
            }

            $jamInstrument->setInstrument($instrument);
            $jamInstrument->setMusician(null);
            $jamInstrument->setJam($this->jam);
            $this->entityManager->persist($jamInstrument);

        }

        return $ids;
    }

    public function setMydata($data)
    {
        $this->jam = $data;
    }
}