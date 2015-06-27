<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Brand;

class LoadBrandsAmpsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $brands = array(
"65amps"
,"Acoustic Control Corporation"
,"Ahed"
,"Ampeg"
,"Ashdown Engineering"
,"Award session"
,"Bad Cat"
,"Behringer"
,"Blackstar Amplification"
,"Bogner Amplification"
,"Budda Amplification"
,"Carlsbro"
,"Carr Amplifiers"
,"Carvin Corporation"
,"Danelectro"
,"Dean Guitars"
,"Diezel"
,"Dr. Z Amplification"
,"Dumble Amplifiers"
,"Earth Sound Research"
,"Echolette"
,"Egnater"
,"Electro-Harmonix"
,"Electromuse"
,"Electrosonic Amplifiers"
,"Electro-Voice"
,"ENGL"
,"Epiphone"
,"Fender"
,"Fryette Amplification"
,"Gabriel (amplifiers)"
,"Gibson Guitar Corporation"
,"Goodsell Amplifiers"
,"Hartke Systems"
,"Hayden (electronics company)"
,"HH Electronics"
,"Hilgen"
,"Hiwatt"
,"Hughes & Kettner"
,"Ibanez"
,"Jennings Musical Instruments"
,"Jess Oliver"
,"Jim Kelley Amplifiers"
,"Johnson Amplification"
,"Kay Musical Instrument Company"
,"Komet Amps"
,"Kona Guitars"
,"Krank Amplification"
,"Kustom Amplification"
,"Laney Amplification"
,"Line 6"
,"Magnatone"
,"Marshall Amplification"
,"Matamp"
,"Matchless Amplifiers"
,"Mesa Boogie"
,"Milbert Amplifiers"
,"Monster "
,"Moody Amplifiers"
,"Multivox Premier"
,"Music Man"
,"Orange Music Electronic Company"
,"Paul Cornford"
,"Peavey Electronics"
,"Phonic Corporation"
,"Pignose"
,"PRS Guitars"
,"Randall Amplifiers"
,"Reason Amplifier Company"
,"Rickenbacker"
,"Rick-Tone"
,"Rivera"
,"Roland Corporation"
,"Sadowsky"
,"Scholz Research & Development, Inc."
,"Schroeder Audio"
,"Smith Custom Amplifiers"
,"Soldano"
,"Soultone"
,"Sound City (company)"
,"Specimen Products"
,"Standel"
,"Suhr Guitars"
,"Sunn"
,"SWR Sound Corporation"
,"Tech 21"
,"Teisco"
,"THD Electronics"
,"Tone King"
,"Trace Elliot"
,"Trainwreck Circuits"
,"Traynor Amplifiers"
,"Univox"
,"Valco"
,"Watkins Electric Music"
,"Voodoo Amplification"
,"Vox"
,"Yamaha Corporation"
,"Yorkville Sound"
        );

        foreach ($brands as $b) {
            $brand = new Brand();
            $brand->setName(trim($b));
            $brand->setParent('amps');
            $manager->persist($brand);
        }

        $manager->flush();
    }
}