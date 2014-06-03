<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Brand;

class LoadBrandsDrumsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $brands = array(
"Adams Musical Instruments"
,"Adoro Drums"
,"Alesis"
,"American Drum Manufacturing Company"
,"Avedis Zildjian Company (AKA Zildjian)"
,"Axis Percussion"
,"Ayotte Drums"
,"Beyerdynamic"
,"Bosphorus Cymbals"
,"Brady Drum Company"
,"Camco Drum Company"
,"CB"
,"Conn-Selmer"
,"Cooperman Fife and Drum Company"
,"Corder Drum Company"
,"Cumbus"
,"D'Addario"
,"DC California"
,"Ddrum"
,"Dixon Drums"
,"Drum Limousine"
,"Drum Workshop (AKA DW)"
,"Drumsound"
,"dsdrum"
,"Dynasty USA"
,"E-MU Systems"
,"Evans"
,"Fibes Drums"
,"Gibraltar Hardware"
,"Gregg Keplinger"
,"Gretsch Drums"
,"Harmony Company"
,"Hayman drum"
,"HB drums"
,"Hohner"
,"Infinity Drumworks"
,"John Grey & Sons"
,"Jupiter Band Instruments"
,"KHS Musical Instruments"
,"King Conga"
,"Kumu Drums"
,"Latin Percussion"
,"Lazer Percussion"
,"Ludwig-Musser"
,"Majestic Percussion"
,"Malletech"
,"Mapex Drums"
,"Meinl Percussion"
,"Noble & Cooley"
,"North Drums"
,"Orange County Drum and Percussion"
,"Pacific Drums and Percussion"
,"Paiste"
,"Peace Drums and Percussion"
,"Pearl Drums"
,"Peavey Electronics"
,"Pork Pie Percussion"
,"Power Beat Percussion"
,"Premier Percussion"
,"Pro-Mark"
,"Remo"
,"Rogers Drums"
,"Roland Corporation"
,"Sabian"
,"San Francisco Drum Company"
,"Sennheiser"
,"Serenity Custom Drums"
,"Shure"
,"Simmons"
,"Sleishman Drum Company"
,"Slingerland Drum Company"
,"Sonor"
,"Sound Percussion"
,"Tama Drums"
,"Taye Drums"
,"Trick Percussion"
,"Trixon Drums"
,"TRX Cymbals "
,"UFIP"
,"Walter Light"
,"Vater"
,"Vic Firth"
,"Vic Firth"
,"Yamaha Drums"
,"Zendrum"
,"Zildjian"
        );

        foreach ($brands as $b) {
            $brand = new Brand();
            $brand->setName(trim($b));
            $brand->setParent('drums');
            $manager->persist($brand);
        }

        $manager->flush();
    }
}