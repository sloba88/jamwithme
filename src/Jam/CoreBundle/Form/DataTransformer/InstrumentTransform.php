<?php
namespace Jam\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class InstrumentTransform implements \Symfony\Component\Form\DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (group) to a string (number).
     *
     * @param  Group|null $group
     * @return string
     */
    public function transform($instrument)
    {
        if (null === $instrument) {
            return null;
        }

        return $instrument->getId();
    }

    /**
     * Transforms a string (number) to an object (group).
     *
     * @param  string $number
     * @return Group|null
     * @throws TransformationFailedException if object (group) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        if ($number == '') {
            return null;
        }

        $instrument = $this->om
            ->getRepository('JamCoreBundle:Instrument')
            ->find($number);
        ;

        if (null === $instrument) {
            throw new TransformationFailedException(sprintf(
                'Instrument with ID "%s" does not exist!',
                $number
            ));
        }

        return $instrument;
    }
}