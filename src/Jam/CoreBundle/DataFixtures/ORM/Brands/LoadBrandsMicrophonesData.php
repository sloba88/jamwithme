<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Brand;

class LoadBrandsMicrophonesData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $brands = array(
"12 Gauge Microphones"
,"3 Zigma Audio"
,"ADK"
,"Advanced Audio Microphones"
,"AEA"
,"AKG Acoustics"
,"Alesis"
,"Apex Electronics"
,"Aphex"
,"Apogee Electronics"
,"Applied Microphone Technology"
,"ART Pro Audio"
,"Aseyer Electric Technology Nanjing Co"
,"Audio-Technica"
,"Audix"
,"Avantone Pro"
,"Avenson Audio"
,"Behringer"
,"Beijing 797 Audio Co. Ltd."
,"beyerdynamic"
,"BIG Sound Microphones"
,"Bing Carbon Microphones"
,"Blackspade Acoustics"
,"Blue Microphones"
,"Bock Audio"
,"Brauner"
,"Byetone"
,"CAD Audio"
,"Carvin Corporation"
,"Cascade Microphones"
,"Cathedral Guitars"
,"Cathedral Pipes"
,"Chameleon Labs"
,"CharterOak Acoustic Devices"
,"Cloud Microphones"
,"Coles Electroacoustics"
,"Core Sound"
,"Crowley and Tripp"
,"DIY Audio Components"
,"DPA"
,"Ear Trumpet Labs"
,"Earthworks Audio"
,"Ehrlund Microphones"
,"Elation Mic Lab"
,"Electro-Voice"
,"Equation Audio"
,"Feilo"
,"FLEA"
,"Focusrite"
,"Gauge Microphones"
,"Golden Age Project"
,"Granelli Audio Labs"
,"Groove Tubes"
,"Hand Crafted Laboratories"
,"Heil Sound"
,"Horch Audio"
,"IK Multimedia"
,"InnerTUBE Audio"
,"Joemeek"
,"Josephson Engineering"
,"JZ Microphones"
,"Karma Audio"
,"Kel Audio"
,"Lampifier Company"
,"Lauten Audio"
,"Lawson Inc"
,"Lewitt Audio"
,"Liberty Microphone & Transducer Co."
,"Lucas Engineering"
,"Manley Laboratories, Inc"
,"Marek Design GmbH"
,"M-Audio"
,"MCA"
,"McHugh Military"
,"Mercenary Audio Mfg"
,"Mesanovic Microphones"
,"Microphone Parts"
,"Microtech Gefell"
,"MicW"
,"Miktek"
,"Milab Microphones"
,"Mojave Audio"
,"Monoprice"
,"Moon Mics, LLC"
,"MXL"
,"Nady"
,"Naiant"
,"Neumann"
,"Nevaton"
,"Ningbo Alctron Electronics Co., Ltd."
,"Ningbo Shengke Electronic Co.,Ltd"
,"Ocean Park Audio Labs"
,"Oktava"
,"Pearl Microphone Laboratory"
,"Pearlman Microphones"
,"Peluso Microphone Lab"
,"RCA"
,"Red Microphones"
,"RMS Audioworks"
,"Ronin Applied Sciences"
,"Royer Labs"
,"RØDE"
,"sage Electronics"
,"Samar Audio Design"
,"Samson"
,"Sanken Microphone Company, Ltd."
,"Schoeps Mikrofone"
,"SE Electronics"
,"Sennheiser Electronics Corporation"
,"Sheng Yue"
,"Shinybox"
,"ShuaiYin"
,"Shure"
,"Silvia Classics"
,"Slate Digital"
,"SM Pro Audio"
,"Sontronics"
,"Sony"
,"Soundelux"
,"Soyuz Microphones"
,"Stedman"
,"Stellar Sounds"
,"Sterling Audio"
,"Studio Projects"
,"Superlux"
,"t.bone"
,"T.H.E. Audio"
,"Tascam"
,"Telefunken"
,"Telefunken Elektroakustik"
,"Thuresson"
,"TNC Audio"
,"TSM"
,"Violet Design Ltd."
,"VTL"
,"Wunder Audio"
,"Yamaha"
        );

        foreach ($brands as $b) {
            $brand = new Brand();
            $brand->setName(trim($b));
            $brand->setParent('microphones');
            $manager->persist($brand);
        }

        $manager->flush();
    }
}