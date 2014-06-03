<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Brand;

class LoadBrandsStringsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $brands = array(
"D'Addario"
,"Dean Markley"
,"Dunlop"
,"Elixir"
,"Ernie Ball"
,"Fender"
,"Ghs"
,"Gibson"
,"Rotosound"
        );

        foreach ($brands as $b) {
            $brand = new Brand();
            $brand->setName(trim($b));
            $brand->setParent('strings');
            $manager->persist($brand);
        }

        $manager->flush();
    }
}