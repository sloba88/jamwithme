<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Genre;
use Jam\CoreBundle\Entity\Instrument;
use Jam\CoreBundle\Entity\InstrumentCategory;

class LoadInstrumentsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $strings = array(
          "Acoustic Guitar"
        , "Ajaeng"
        , "Autoharp"
        , "Avoustic Bass"
        , "Balalaika"
        , "Banhu"
        , "Banjo"
        , "Baritone Guitar"
        , "Baryton"
        , "Bazantar"
        , "Bowed psaltery"
        , "Cavaquinho"
        , "Cello"
        , "Chanzy"
        , "Classical Guitar"
        , "Crwth"
        , "Dahu"
        , "Dan Bau"
        , "Đàn gáo"
        , "Dilrupa"
        , "Division viol"
        , "Diyingehu"
        , "Dobro"
        , "Double bass"
        , "Eight-string Guitar"
        , "Electric Bass"
        , "Electric Guitar"
        , "Erxian"
        , "Esraj"
        , "Flamenco Guitar"
        , "Gadulka"
        , "Gaohu"
        , "Gehu"
        , "Ghaychak"
        , "Goje "
        , "Gudok "
        , "Guitar "
        , "Guitar Synthesizer"
        , "Gusle "
        , "Haegeum "
        , "Harp"
        , "Harp Guitar"
        , "Harpsichord"
        , "Huluhu "
        , "Huqin "
        , "Hurdy gurdy "
        , "Igil "
        , "Imzad "
        , "Jinghu "
        , "Jouhikko "
        , "Kantele"
        , "Kemenche "
        , "Lap Steel"
        , "Laruan "
        , "Leiqin "
        , "Lirone "
        , "Lute"
        , "Lyra viol "
        , "Maguhu "
        , "Mandolin"
        , "Masenqo "
        , "Morin khuur "
        , "Nyckelharpa "
        , "Octobass "
        , "Pedal Steel"
        , "Piccolo Violino"
        , "Psalmodicon "
        , "Rebab "
        , "Rebec "
        , "Sarangi "
        , "Sarinda "
        , "Saw sam sai "
        , "Seven-string Guitar"
        , "Sihu "
        , "Sitar"
        , "Slide Guitar"
        , "Steel Guitar"
        , "Tenor Viola"
        , "Tro "
        );

        $category = new InstrumentCategory();
        $category->setName("Strings");

        foreach ($strings as $i) {
            $instrument = new Instrument();
            $instrument->setName(trim($i));
            $instrument->setCategory($category);
            $manager->persist($instrument);
        }

        $percussions = array(
            "Ashiko "
            ,"Basler drum "
            ,"Bass drum "
            ,"Bell "
            ,"Bongo drum "
            ,"Castanet "
            ,"Celesta "
            ,"Claves "
            ,"Conga "
            ,"Cymbal "
            ,"Dabakan "
            ,"Djembe "
            ,"Drum "
            ,"Drums "
            ,"Electronic drum "
            ,"Flexatone "
            ,"Ganza "
            ,"Gong "
            ,"Gong bass drum "
            ,"Güiro "
            ,"Hand drum "
            ,"Hi-hat "
            ,"Long drum "
            ,"Maracas "
            ,"Marching percussion "
            ,"Marimba "
            ,"Membranophones"
            ,"Metallophone "
            ,"Pandeiro "
            ,"Rainstick "
            ,"Ratchet "
            ,"Shaker "
            ,"Silimba "
            ,"Snare drum "
            ,"Steelpan "
            ,"Tabla "
            ,"Tambourine "
            ,"Temple block "
            ,"Tenor drum "
            ,"Thon-rammana "
            ,"Timbales "
            ,"Timpani "
            ,"Tom-tom drum "
            ,"Tonbak "
            ,"Triangle "
            ,"Tubular bell "
            ,"Tuned Percussion "
            ,"Vibraphone "
            ,"Wind machine "
            ,"Wood block "
            ,"Xylophone "
            ,"Zendrum "
        );

        $category = new InstrumentCategory();
        $category->setName("Percussions");

        foreach ($percussions as $i) {
            $instrument = new Instrument();
            $instrument->setName(trim($i));
            $instrument->setCategory($category);
            $manager->persist($instrument);
        }

        $keys = array(
            "Accordion"
            ,"Barrel piano "
            ,"Celesta"
            ,"Clavichord "
            ,"Clavinet"
            ,"Concertina"
            ,"Dulcitone "
            ,"Electric piano"
            ,"Electronic keyboard "
            ,"Electronic Organ"
            ,"Grand piano "
            ,"Hammond"
            ,"Hammond Organ"
            ,"Harmonium "
            ,"Harpsichord "
            ,"Keyboard bass "
            ,"Keyboard controller "
            ,"Marimba"
            ,"Mellotron"
            ,"Melodica"
            ,"Novachord "
            ,"Ondes Martenot "
            ,"Orchestron "
            ,"Organ "
            ,"Piano "
            ,"Player piano "
            ,"Reed Organ"
            ,"Silent piano "
            ,"Spinet "
            ,"Square piano "
            ,"Synth"
            ,"Synthesizer "
            ,"Theremin "
            ,"Toy piano "
            ,"Trautonium "
            ,"Virginals "
            ,"Xylophone"
        );

        $category = new InstrumentCategory();
        $category->setName("Keys");

        foreach ($keys as $i) {
            $instrument = new Instrument();
            $instrument->setName(trim($i));
            $instrument->setCategory($category);
            $manager->persist($instrument);
        }
        
        $wind = array(
            "Alphorn"
            ,"Alto flute" 
            ,"Babpipe"
            ,"Bamboo flute" 
            ,"Baroque trumpet" 
            ,"Bass flute "
            ,"Bass oboe "
            ,"Bass trumpet" 
            ,"Bassoon "
            ,"Bazooka"
            ,"Bombard "
            ,"Buisine "
            ,"Claghorn "
            ,"Clarinet "
            ,"Clarion "
            ,"Conch"
            ,"Contra-alto flute "
            ,"Contrabass flute "
            ,"Contrabass oboe "
            ,"Cor anglais "
            ,"Cornett "
            ,"Diple "
            ,"Double contrabass flute "
            ,"Duct flutes "
            ,"Duduk "
            ,"Fife "
            ,"Fipple "
            ,"Fipple Flute"
            ,"Flute "
            ,"Flûte d'amour "
            ,"Fue "
            ,"Harmonica"
            ,"Hyperbass flute "
            ,"Irish flute "
            ,"Keyed trumpet "
            ,"Melodica "
            ,"Mouth organ "
            ,"Native American flute "
            ,"Natural trumpet "
            ,"Nose flute "
            ,"Oboe "
            ,"Ocarina"
            ,"Ophicleide "
            ,"Palendag "
            ,"Piccolo "
            ,"Piccolo trumpet "
            ,"Pipe "
            ,"Pku "
            ,"Pocket trumpet "
            ,"Pommer "
            ,"Rackett "
            ,"Reclam de xeremies "
            ,"Recorder "
            ,"Reed contrabass "
            ,"Reeds "
            ,"Sarrusophone "
            ,"Saxophone "
            ,"Serpent "
            ,"Shakuhachi "
            ,"Shawm "
            ,"Slide trumpet "
            ,"Soprano flute "
            ,"Stradivarius trumpet "
            ,"Subcontrabass flute "
            ,"Suona"
            ,"Tin whistle "
            ,"Treble flute "
            ,"Trombone"
            ,"Trumpet"
            ,"Tuba"
            ,"Tube trumpet "
            ,"Tumpong "
            ,"Uilleann pipes "
            ,"Washint "
            ,"Western concert flute "
            ,"Whistle"
            ,"Xiao "
        );

        $category = new InstrumentCategory();
        $category->setName("Wind Instruments");

        foreach ($wind as $i) {
            $instrument = new Instrument();
            $instrument->setName(trim($i));
            $instrument->setCategory($category);
            $manager->persist($instrument);
        }

        $vocals = array(
            "Background Vocals"
            ,"Baritone"
            ,"Bass"
            ,"Beatboxing"
            ,"Contralto"
            ,"Countertenor"
            ,"Mezzo-soprano"
            ,"Rapping"
            ,"Rhyming"
            ,"Soprano"
            ,"Tenor"
            ,"Vocals"
        );
        
        $category = new InstrumentCategory();
        $category->setName("Vocals");

        foreach ($vocals as $i) {
            $instrument = new Instrument();
            $instrument->setName(trim($i));
            $instrument->setCategory($category);
            $manager->persist($instrument);
        }

        $other = array(
        "DJ"
        ,"Producer"
        ,"Recording Studio"
        ,"Sampler"
        );

        $category = new InstrumentCategory();
        $category->setName("Other");

        foreach ($other as $i) {
            $instrument = new Instrument();
            $instrument->setName(trim($i));
            $instrument->setCategory($category);
            $manager->persist($instrument);
        }

        $manager->flush();
    }
}