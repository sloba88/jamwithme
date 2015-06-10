<?php
namespace Jam\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class BrandTransform implements DataTransformerInterface
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
    public function transform($brands)
    {
        if (null === $brands) {
            return "";
        }

        $brand = '';
        foreach ($brands AS $k => $a){
            if($k!=0) $brand .= ',';
            $brand .= $a->getName();

        }

        return $brand;
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
        $brandsCollection = new ArrayCollection();

        if (!$name) {
            return $brandsCollection;
        }

        $names = explode(",", $name);
        foreach($names AS $name){

            $brand = $this->entityManager
                ->getRepository('JamCoreBundle:Brand')
                ->findOneBy(array('name' => $name));

            $brandsCollection->add($brand);
        }

        if (null === $brand) {
            throw new TransformationFailedException(sprintf(
                'Brand with ID "%s" does not exist!',
                $name
            ));
        }

        return $brandsCollection;
    }
}